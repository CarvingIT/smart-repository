<?php

namespace App\Services;

use App\Collection;
use App\CollectionSubscription;
use App\Notifications\CollectionSubscriptionCreated;
use App\Notifications\CollectionSubscriptionPaymentCompleted;
use App\Permission;
use App\User;
use App\UserPermission;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class CollectionSubscriptionService
{
    public function subscriptionConfig(Collection $collection): object
    {
        return json_decode($collection->column_config) ?? new \stdClass();
    }

    public function isEnabled(Collection $collection): bool
    {
        $config = $this->subscriptionConfig($collection);

        return $collection->type === 'Members Only' && !empty($config->soap_subscription_enabled);
    }

    public function paymentUri(Collection $collection): string
    {
        $config = $this->subscriptionConfig($collection);

        return trim((string) ($config->soap_subscription_payment_uri ?? ''));
    }

    public function notificationEmails(Collection $collection): array
    {
        $config = $this->subscriptionConfig($collection);
        $emails = trim((string) ($config->soap_subscription_notification_email ?? ''));

        if ($emails === '') {
            return [];
        }

        return array_values(array_filter(array_map('trim', preg_split('/\s*,\s*/', $emails))));
    }

    public function createPendingSubscription(Collection $collection, array $input, ?User $actor = null): CollectionSubscription
    {
        $config = $this->subscriptionConfig($collection);

        $subscription = new CollectionSubscription();
        $subscription->collection_id = $collection->id;
        $subscription->user_id = $actor ? $actor->id : null;
        $subscription->user_email = $input['email'];
        $subscription->user_name = $input['name'] ?? null;
        $subscription->user_mobile = $input['mobile'] ?? null;
        $subscription->amount = $this->subscriptionAmount($config);
        $subscription->payment_status = 'pending';
        $subscription->till_date = $this->resolveTillDate($config);
        $subscription->payment_payload = json_encode($input);
        $subscription->save();

        $subscription->challan = $this->generateChallanNumber($subscription, $config);
        $subscription->save();

        return $subscription->fresh(['collection', 'user']);
    }

    public function successfulSubscriptionFor(Collection $collection, ?User $actor = null, ?string $email = null): ?CollectionSubscription
    {
        $query = CollectionSubscription::query()
            ->where('collection_id', $collection->id)
            ->where('payment_status', 'success');

        $query->where(function ($subQuery) use ($actor, $email) {
            if ($actor) {
                $subQuery->where('user_id', $actor->id);
            }

            if (!empty($email)) {
                $subQuery->orWhereRaw('LOWER(user_email) = ?', [strtolower($email)]);
            }
        });

        return $query->orderByDesc('paid_at')->orderByDesc('id')->first();
    }

    public function buildGatewayPayload(CollectionSubscription $subscription, Collection $collection): array
    {
        $config = $this->subscriptionConfig($collection);
        $reconciliationUri = trim((string) ($config->soap_subscription_reconciliation_uri ?? ''));
        $applicationId   = trim((string) ($config->soap_subscription_application_id   ?? ''));
        $applicationName = trim((string) ($config->soap_subscription_application_name ?? $collection->name));

        // SPPU payment gateway expects these exact field names.
        // See Technical Payment Integration Document for the full contract.
        return [
            'ApplicationName' => $applicationName,
            'ApplicationID'   => $applicationId,
            'ChallanNo'       => (string) $subscription->challan,
            'FullName'        => (string) $subscription->user_name,
            'MobileNo'        => (string) $subscription->user_mobile,
            'EmailID'         => (string) $subscription->user_email,
            'Amount'          => number_format((float) $subscription->amount, 2, '.', ''),
            // Internal fields kept as hidden inputs so our reconcile endpoint
            // can look up the subscription on callback.
            'subscription_id'     => $subscription->id,
            'reconciliation_uri'  => $reconciliationUri !== '' ? $reconciliationUri : URL::to('/collection/' . $collection->id . '/soap-subscription/reconcile'),
        ];
    }

    /**
     * Encrypts the given clear text (ChallanNo) to match SPPU's AES C# encryption logic.
     * C# logic uses Rfc2898DeriveBytes (PBKDF2 with SHA1, 1000 iterations) with a specific salt
     * to derive a 256-bit Key and 128-bit IV from the EncryptionKey.
     */
    public function encryptChallanNo(string $clearText): string
    {
        $encryptionKey = "MAKV2SPBNI99212";
        // C# Encoding.Unicode is UTF-16LE
        $clearBytes = mb_convert_encoding($clearText, 'UTF-16LE', 'UTF-8');

        // SPPU's salt from C#: new byte[] { 0x49, 0x76, 0x61, 0x6e, 0x20, 0x4d, 0x65, 0x64, 0x76, 0x65, 0x64, 0x65, 0x76 }
        // This decodes to "Ivan Medved"
        $salt = "Ivan Medvedev";

        // Derive Key and IV (48 bytes total: 32 for key, 16 for IV)
        $derived = hash_pbkdf2("sha1", $encryptionKey, $salt, 1000, 48, true);

        $key = substr($derived, 0, 32);
        $iv  = substr($derived, 32, 16);

        // Encrypt using AES-256-CBC
        $encrypted = openssl_encrypt($clearBytes, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);

        return base64_encode($encrypted);
    }

    /**
     * Call the SPPU GetPaymentDetails() registration endpoint before opening
     * the payment page. Returns true on success, false (or throws) on failure.
     *
     * SPPU requires the challan to be registered server-side via their
     * SOAP endpoint before the browser is sent to the payment page.
     */
    public function registerWithSppuGateway(CollectionSubscription $subscription, Collection $collection): bool
    {
        $payload = $this->buildGatewayPayload($subscription, $collection);

        // Construct the XML required by SPPU
        $xmlParam = htmlspecialchars("<Challan>
<ApplicationName>{$payload['ApplicationName']}</ApplicationName>
<ApplicationID>{$payload['ApplicationID']}</ApplicationID>
<ChallanNo>{$payload['ChallanNo']}</ChallanNo>
<FullName>{$payload['FullName']}</FullName>
<MobileNo>{$payload['MobileNo']}</MobileNo>
<EmailID>{$payload['EmailID']}</EmailID>
<Amount>{$payload['Amount']}</Amount>
<Address1></Address1>
<Address2></Address2>
<City></City>
<Pincode></Pincode>
<State></State>
<Country></Country>
<ChallanDate>" . date('Y-m-d\TH:i:s.v') . "</ChallanDate>
<IPAddress>" . (request()->ip() ?? '127.0.0.1') . "</IPAddress>
</Challan>");

        $soapEnvelope = '<?xml version="1.0" encoding="utf-8"?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <GetPaymentDetails xmlns="http://tempuri.org/">
      <xml>' . $xmlParam . '</xml>
    </GetPaymentDetails>
  </soap:Body>
</soap:Envelope>';

        try {
            $serviceUrl = 'https://ops.unipune.ac.in/sppupgway/sppupgway.asmx';
            
            $response = Http::timeout(15)
                ->withHeaders([
                    'Content-Type' => 'text/xml; charset=utf-8',
                    'SOAPAction'   => '"http://tempuri.org/GetPaymentDetails"'
                ])
                ->withoutVerifying()
                ->send('POST', $serviceUrl, [
                    'body' => $soapEnvelope
                ]);

            if ($response->failed() || !str_contains($response->body(), '<GetPaymentDetailsResult>1</GetPaymentDetailsResult>')) {
                Log::warning('SPPU GetPaymentDetails() failed or returned non-1 result.', [
                    'subscription_id' => $subscription->id,
                    'status'          => $response->status(),
                    'body'            => $response->body(),
                ]);
                return false;
            }

            Log::info('SPPU GetPaymentDetails() registration succeeded.', [
                'subscription_id' => $subscription->id,
                'challan_no'      => $payload['ChallanNo']
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Exception during SPPU GetPaymentDetails() call.', [
                'subscription_id' => $subscription->id,
                'error'           => $e->getMessage(),
            ]);
            return false;
        }
    }

    public function reconcile(CollectionSubscription $subscription, array $payload): CollectionSubscription
    {
        return DB::transaction(function () use ($subscription, $payload) {
            $lockedSubscription = CollectionSubscription::query()
                ->whereKey($subscription->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedSubscription->payment_status === 'success') {
                return $lockedSubscription->fresh(['collection', 'user']);
            }

            $status = $this->normalizeStatus($payload['payment_status'] ?? $payload['status'] ?? null);
            $postedAmount = $payload['amount'] ?? $payload['Amount'] ?? $payload['transaction_amount'] ?? null;

            if ($status === 'success' && $postedAmount !== null && $lockedSubscription->amount !== null) {
                if ((float) $postedAmount !== (float) $lockedSubscription->amount) {
                    $status = 'failed';
                }
            }

            $lockedSubscription->payment_status = $status;
            $lockedSubscription->payment_reference = $payload['transaction_id'] ?? $payload['reference_no'] ?? $payload['reference'] ?? null;
            $lockedSubscription->reconciliation_payload = json_encode($payload);

            if ($status === 'success') {
                $lockedSubscription->paid_at = $lockedSubscription->paid_at ?: now();
                $lockedSubscription->save();
                $user = $this->grantAccess($lockedSubscription);
                $this->notifySuccess($lockedSubscription, $user);
            } else {
                $lockedSubscription->save();
            }

            return $lockedSubscription->fresh(['collection', 'user']);
        });
    }

    public function grantAccess(CollectionSubscription $subscription): User
    {
        $collection = $subscription->collection()->firstOrFail();
        $config = $this->subscriptionConfig($collection);
        $tillDate = $subscription->till_date ?: $this->resolveTillDate($config);

        $user = $subscription->user ?: User::where('email', $subscription->user_email)->first();
        $createdUser = false;

        if (!$user) {
            $createdUser = true;
            $user = User::create([
                'name' => $subscription->user_name ?: Str::before($subscription->user_email, '@'),
                'email' => $subscription->user_email,
                'password' => bcrypt(Str::random(40)),
                'email_verified_at' => now(),
            ]);

            $token = Password::broker()->createToken($user);
            $resetUrl = URL::to('/password/reset/' . $token . '?email=' . urlencode($user->email));
            $loginUrl = URL::to('/login');
            $user->notify(new CollectionSubscriptionCreated($resetUrl, $loginUrl));
        }

        $subscription->user_id = $user->id;
        $subscription->save();

        $viewPermission = Permission::firstOrCreate(
            ['name' => 'VIEW'],
            ['description' => 'Can view any content']
        );

        UserPermission::updateOrCreate(
            [
                'user_id' => $user->id,
                'collection_id' => $collection->id,
                'permission_id' => $viewPermission->id,
            ],
            [
                'till_date' => $tillDate,
            ]
        );

        if (empty($user->email_verified_at)) {
            $user->email_verified_at = now();
            $user->save();
        }

        return $user;
    }

    public function notifySuccess(CollectionSubscription $subscription, User $user): void
    {
        $collection = $subscription->collection()->firstOrFail();
        $notification = new CollectionSubscriptionPaymentCompleted($subscription->fresh(['collection']));

        $user->notify($notification);

        foreach ($this->notificationEmails($collection) as $email) {
            if (strtolower($email) === strtolower($user->email)) {
                continue;
            }

            Notification::route('mail', $email)->notify($notification);
        }
    }

    public function applicationId(Collection $collection): string
    {
        $config = $this->subscriptionConfig($collection);
        return trim((string) ($config->soap_subscription_application_id ?? ''));
    }

    public function applicationName(Collection $collection): string
    {
        $config = $this->subscriptionConfig($collection);
        $name = trim((string) ($config->soap_subscription_application_name ?? ''));
        return $name !== '' ? $name : $collection->name;
    }

    protected function subscriptionAmount(object $config): ?float
    {
        $amount = trim((string) ($config->soap_subscription_amount ?? ''));

        return $amount === '' ? null : (float) $amount;
    }

    protected function resolveTillDate(object $config): ?string
    {
        $validDays = (int) ($config->soap_subscription_valid_days ?? 365);
        if ($validDays < 1) {
            $validDays = 365;
        }

        return Carbon::now()->addDays($validDays)->format('Y-m-d');
    }

    protected function generateChallanNumber(CollectionSubscription $subscription, object $config): string
    {
        $template = trim((string) ($config->soap_subscription_challan_template ?? ''));

        $sequenceLength = (int) ($config->soap_subscription_sequence_length ?? 5);
        if ($sequenceLength < 1) {
            $sequenceLength = 5;
        }

        $processCode = trim((string) ($config->soap_subscription_process_code ?? ''));
        if ($processCode === '') {
            $processCode = '00';
        }

        $year = now()->format('y');
        $monthCode = str_pad((string) ((int) now()->format('n') + 36), 2, '0', STR_PAD_LEFT);
        $prefix = $year . str_pad($processCode, 2, '0', STR_PAD_LEFT) . $monthCode;

        $lastSequence = CollectionSubscription::query()
            ->where('collection_id', $subscription->collection_id)
            ->where('challan', 'like', $prefix . '%')
            ->whereNotNull('challan')
            ->orderByDesc('id')
            ->value('challan');

        $sequence = 1;
        if (!empty($lastSequence) && strlen($lastSequence) > strlen($prefix)) {
            $sequence = ((int) substr($lastSequence, -$sequenceLength)) + 1;
        }

        $sequence = str_pad((string) $sequence, $sequenceLength, '0', STR_PAD_LEFT);

        if ($template === '') {
            return $prefix . $sequence;
        }

        $replacements = [
            '{{year}}' => $year,
            '{{process_code}}' => str_pad($processCode, 2, '0', STR_PAD_LEFT),
            '{{month}}' => $monthCode,
            '{{sequence}}' => $sequence,
            '{{amount}}' => number_format((float) ($subscription->amount ?? 0), 2, '.', ''),
            '{{collection_id}}' => (string) $subscription->collection_id,
            '{{subscription_id}}' => (string) $subscription->id,
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $template);
    }

    protected function normalizeStatus($status): string
    {
        $normalized = strtolower(trim((string) $status));

        if (in_array($normalized, ['success', 'paid', 'completed', 'successful'], true)) {
            return 'success';
        }

        if (in_array($normalized, ['failed', 'failure'], true)) {
            return 'failed';
        }

        return $normalized !== '' ? $normalized : 'pending';
    }
}