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

        return [
            'subscription_id' => $subscription->id,
            'collection_id' => $collection->id,
            'collection_name' => $collection->name,
            'challan' => $subscription->challan,
            'amount' => $subscription->amount,
            'name' => $subscription->user_name,
            'email' => $subscription->user_email,
            'reconciliation_uri' => $reconciliationUri !== '' ? $reconciliationUri : URL::to('/collection/' . $collection->id . '/soap-subscription/reconcile'),
        ];
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