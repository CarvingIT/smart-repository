<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureLoginTwoFactorVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Only enforce login 2FA when explicitly enabled.
        if ((int) env('ENABLE_2FA_LOGIN', 0) !== 1) {
            return $next($request);
        }

        $user = $request->user();

        // If user is not authenticated, let them proceed (will be redirected by auth middleware)
        if (!$user) {
            return $next($request);
        }

        // If user has 2FA enabled, check if they've verified it in this session
        if ($user->hasTwoFactorEnabled()) {
            $verified = $request->session()->get('login_two_factor_verified_at');
            if (!$verified) {
                return redirect()->route('login.two-factor.challenge');
            }
        }

        return $next($request);
    }
}
