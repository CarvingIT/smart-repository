<?php

namespace App\Http\Controllers;

use App\Notifications\CollectionSubscriptionCreated;
use App\Permission;
use App\User;
use App\UserPermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class ApiCollectionSubscriptionController extends Controller
{
    /**
     * Subscribe a user to one or more collections with VIEW permission.
     */
    public function subscribe(Request $request)
    {
        $request->merge([
            'collection_id' => $this->normalizeCollectionIds($request->input('collection_id')),
        ]);

        $validated = $request->validate([
            'email' => ['required', 'email'],
            'collection_id' => ['required', 'array', 'min:1'],
            'collection_id.*' => ['integer', 'exists:collections,id'],
            'till_date' => ['required', 'date'],
            'name' => ['nullable', 'string', 'max:255'],
        ]);

        $viewPermission = Permission::where('name', 'VIEW')->first();
        if (!$viewPermission) {
            return response()->json([
                'message' => 'VIEW permission is not configured.',
            ], 500);
        }

        $created = false;
        $resetLinkStatus = null;
        $user = null;

        DB::transaction(function () use ($validated, $viewPermission, &$created, &$resetLinkStatus, &$user) {
            $user = User::where('email', $validated['email'])->first();

            if (!$user) {
                $created = true;
                $user = User::create([
                    'name' => $validated['name'] ?? Str::before($validated['email'], '@'),
                    'email' => $validated['email'],
                    // Random password; user is expected to set their own via reset link.
                    'password' => Hash::make(Str::random(40)),
                    'email_verified_at' => now(),
                ]);

                $token = Password::broker()->createToken($user);
                $resetUrl = URL::to('/password/reset/' . $token . '?email=' . urlencode($user->email));
                $loginUrl = URL::to('/login');

                $user->notify(new CollectionSubscriptionCreated($resetUrl, $loginUrl));
                $resetLinkStatus = Password::RESET_LINK_SENT;
            }

            foreach (array_unique($validated['collection_id']) as $collectionId) {
                UserPermission::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'collection_id' => $collectionId,
                        'permission_id' => $viewPermission->id,
                    ],
                    [
                        'till_date' => $validated['till_date'],
                    ]
                );
            }
        });

        return response()->json([
            'message' => 'User subscription saved successfully.',
            'user_created' => $created,
            'email' => $validated['email'],
            'collection_ids' => array_values(array_unique($validated['collection_id'])),
            'permission' => 'VIEW',
            'till_date' => $validated['till_date'],
            'password_reset_email_status' => $resetLinkStatus,
        ]);
    }

    /**
     * Allow JSON arrays as well as common form-data formats.
     *
     * @param mixed $value
     * @return array
     */
    private function normalizeCollectionIds($value)
    {
        if (is_array($value)) {
            return $value;
        }

        if ($value === null || $value === '') {
            return [];
        }

        if (is_numeric($value)) {
            return [(int) $value];
        }

        if (is_string($value)) {
            $trimmed = trim($value);
            if ($trimmed === '') {
                return [];
            }

            $decoded = json_decode($trimmed, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }

            if (strpos($trimmed, ',') !== false) {
                return array_values(array_filter(array_map('trim', explode(',', $trimmed)), function ($item) {
                    return $item !== '';
                }));
            }

            return [$trimmed];
        }

        return [];
    }
}
