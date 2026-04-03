<?php

namespace App\Http\Middleware;

use App\Collection;

trait EnforcesCollectionTwoFactor
{
    protected function enforceCollectionTwoFactor($request, ?int $collectionId)
    {
        if (!$collectionId) {
            return null;
        }

        $collection = Collection::find($collectionId);
        if (!$collection || !$collection->isTwoFactorRequired()) {
            return null;
        }

        $user = $request->user();
        if (!$user) {
            return redirect()->guest(route('login'));
        }

        if (!$user->hasTwoFactorEnabled()) {
            return redirect()->route('profile.edit')
                ->with('alert-danger', 'This collection requires 2FA. Please enable TOTP authentication in your profile first.');
        }

        $verificationKey = 'two_factor_verified_' . (int) $collectionId;
        $lastVerificationTime = $request->session()->get($verificationKey);
        
        if ($lastVerificationTime) {
            return null;
        }

        return redirect()->route('two-factor.challenge.form', [
            'collection_id' => $collectionId,
            'intended' => $request->fullUrl(),
        ]);
    }
}
