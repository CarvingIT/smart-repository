<?php

namespace App\Http\Controllers;

use App\Services\TotpService;
use App\Http\Requests\ProfileRequest;
use App\Http\Requests\PasswordRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    /**
     * Show the form for editing the profile.
     *
     * @return \Illuminate\View\View
     */
    public function edit(TotpService $totpService)
    {
        $user = auth()->user();
        $decryptedSecret = null;
        $provisioningUri = null;
        $qrCodeUrl = null;

        if (!empty($user->two_factor_secret)) {
            try {
                $decryptedSecret = Crypt::decryptString($user->two_factor_secret);
                $issuer = env('APP_NAME', 'SMART REPOSITORY');
                $provisioningUri = $totpService->getProvisioningUri($user->email, $decryptedSecret, $issuer);
                $qrCodeUrl = $totpService->getQrCodeUrl($provisioningUri);
            } catch (\Exception $e) {
                $decryptedSecret = null;
            }
        }

        return view('profile.edit', [
            'twoFactorSecret' => $decryptedSecret,
            'twoFactorProvisioningUri' => $provisioningUri,
            'twoFactorQrCodeUrl' => $qrCodeUrl,
        ]);
    }

    /**
     * Update the profile
     *
     * @param  \App\Http\Requests\ProfileRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(ProfileRequest $request)
    {
        auth()->user()->update($request->all());

        return back()->withStatus(__('Profile successfully updated.'));
    }

    /**
     * Change the password
     *
     * @param  \App\Http\Requests\PasswordRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function password(PasswordRequest $request)
    {
        auth()->user()->update(['password' => Hash::make($request->get('password'))]);

        return back()->withStatusPassword(__('Password successfully updated.'));
    }

    public function setupTwoFactor(TotpService $totpService)
    {
        $user = auth()->user();

        $secret = $totpService->generateSecret();
        $user->two_factor_secret = Crypt::encryptString($secret);
        $user->two_factor_enabled_at = null;
        $user->save();

        return redirect()->route('profile.edit')->with('status', '2FA setup initialized. Scan the QR code and confirm with a 6-digit code.');
    }

    public function enableTwoFactor(Request $request, TotpService $totpService)
    {
        $request->validate([
            'code' => ['required', 'digits:6'],
        ]);

        $user = auth()->user();
        if (empty($user->two_factor_secret)) {
            return redirect()->route('profile.edit')->with('alert-danger', 'Please click "Generate QR" before enabling 2FA.');
        }

        try {
            $secret = Crypt::decryptString($user->two_factor_secret);
        } catch (\Exception $e) {
            return redirect()->route('profile.edit')->with('alert-danger', 'Unable to read your 2FA setup. Please generate a new QR code.');
        }

        if (!$totpService->verifyCode($secret, $request->input('code'))) {
            return redirect()->route('profile.edit')->withErrors(['code' => 'Invalid authentication code.'])->withInput();
        }

        $user->two_factor_enabled_at = now();
        $user->save();

        return redirect()->route('profile.edit')->with('status', 'Two-factor authentication enabled successfully.');
    }

    public function disableTwoFactor(Request $request)
    {
        $user = auth()->user();
        $user->two_factor_secret = null;
        $user->two_factor_enabled_at = null;
        $user->save();

        foreach (array_keys($request->session()->all()) as $sessionKey) {
            if (strpos($sessionKey, 'two_factor_verified_') === 0) {
                $request->session()->forget($sessionKey);
            }
        }

        return redirect()->route('profile.edit')->with('status', 'Two-factor authentication disabled successfully.');
    }
}
