<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\TotpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class LoginTwoFactorController extends Controller
{
    /**
     * Show the 2FA challenge form during login.
     */
    public function showChallenge(Request $request)
    {
        $user = $request->user();

        // Redirect if user doesn't have 2FA enabled
        if (!$user || !$user->hasTwoFactorEnabled()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login-two-factor-challenge', [
            'intended' => $request->query('intended', '/collections'),
        ]);
    }

    /**
     * Verify the 2FA code provided by user during login.
     */
    public function verify(Request $request, TotpService $totpService)
    {
        $request->validate([
            'code' => ['required', 'digits:6'],
            'intended' => ['nullable', 'string'],
        ]);

        $user = $request->user();

        // Check if user has 2FA enabled
        if (!$user || !$user->hasTwoFactorEnabled() || empty($user->two_factor_secret)) {
            return redirect()->route('login')->with('alert-danger', 'Please enable 2FA in your profile first.');
        }

        try {
            $secret = Crypt::decryptString($user->two_factor_secret);
        } catch (\Exception $e) {
            return redirect()->route('login')->with('alert-danger', 'Unable to read your 2FA setup. Please configure 2FA again.');
        }

        // Verify the code
        if (!$totpService->verifyCode($secret, $request->input('code'))) {
            return back()->withErrors(['code' => 'Invalid authentication code. Please try again.'])->withInput();
        }

        // Mark 2FA as verified in this session
        $request->session()->put('login_two_factor_verified_at', now());
        $request->session()->save();

        // Redirect to intended URL or dashboard
        $intended = $request->input('intended', '/collections');
        return redirect($this->sanitizeUrl($intended));
    }

    /**
     * Sanitize the intended URL to prevent open redirect vulnerabilities.
     */
    private function sanitizeUrl(?string $url): string
    {
        if (empty($url)) {
            return '/collections';
        }

        $parts = parse_url($url);
        if ($parts === false) {
            return '/collections';
        }

        $path = $parts['path'] ?? '/collections';
        
        // Only allow internal URLs
        if (strpos($path, '/') !== 0) {
            return '/collections';
        }

        return $path;
    }
}
