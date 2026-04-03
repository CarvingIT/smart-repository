<?php

namespace App\Http\Controllers;

use App\Collection;
use App\Services\TotpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class TwoFactorController extends Controller
{
    public function showChallengeForm(Request $request, int $collection_id)
    {
        $user = $request->user();
        $collection = Collection::findOrFail($collection_id);

        if (!$collection->isTwoFactorRequired()) {
            return redirect($this->resolveIntendedUrl($request->query('intended')));
        }

        if (!$user || !$user->hasTwoFactorEnabled()) {
            return redirect()->route('profile.edit')
                ->with('alert-danger', 'Please enable 2FA in your profile before accessing this collection.');
        }

        return view('auth.two-factor-challenge', [
            'collection' => $collection,
            'intended' => $request->query('intended', '/collections'),
        ]);
    }

    public function verifyChallenge(Request $request, int $collection_id, TotpService $totpService)
    {
        $request->validate([
            'code' => ['required', 'digits:6'],
            'intended' => ['nullable', 'string'],
        ]);

        $user = $request->user();
        $collection = Collection::findOrFail($collection_id);

        if (!$collection->isTwoFactorRequired()) {
            return redirect($this->resolveIntendedUrl($request->input('intended')));
        }

        if (!$user || !$user->hasTwoFactorEnabled() || empty($user->two_factor_secret)) {
            return redirect()->route('profile.edit')
                ->with('alert-danger', 'Please enable 2FA in your profile before accessing this collection.');
        }

        try {
            $secret = Crypt::decryptString($user->two_factor_secret);
        } catch (\Exception $e) {
            return redirect()->route('profile.edit')
                ->with('alert-danger', 'Unable to read your 2FA setup. Please configure 2FA again.');
        }

        if (!$totpService->verifyCode($secret, $request->input('code'))) {
            return back()->withErrors(['code' => 'Invalid authentication code. Please try again.'])->withInput();
        }

        $verificationKey = 'two_factor_verified_' . (int) $collection->id;
        $request->session()->put($verificationKey, time());
        $request->session()->save();

        return redirect($this->resolveIntendedUrl($request->input('intended')));
    }

    private function resolveIntendedUrl(?string $url): string
    {
        if (empty($url)) {
            return '/collections';
        }

        $parts = parse_url($url);
        if ($parts === false) {
            return '/collections';
        }

        $path = $parts['path'] ?? '/collections';
        $query = isset($parts['query']) ? '?' . $parts['query'] : '';

        return $path . $query;
    }
}
