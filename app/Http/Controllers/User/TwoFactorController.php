<?php
/**
 * Statalog - Open source web analytics.
 * https://statalog.com
 * @license AGPL-3.0
 */

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\TwoFactorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TwoFactorController extends Controller
{
    public function __construct(protected TwoFactorService $service) {}

    private function denyDemo(): ?RedirectResponse
    {
        if (session('is_demo')) {
            return redirect()->route('user.profile.edit')->with('status', 'demo-blocked');
        }
        return null;
    }

    /** Generate a secret and show the QR setup screen. */
    public function start(Request $request): RedirectResponse
    {
        if ($r = $this->denyDemo()) return $r;
        $this->service->generateSecret($request->user());
        return redirect()->route('user.profile.edit')->with('tfa_mode', 'setup');
    }

    /** User entered a 6-digit code from their authenticator — confirm and persist. */
    public function confirm(Request $request): RedirectResponse
    {
        if ($r = $this->denyDemo()) return $r;
        $data = $request->validate(['code' => ['required', 'string', 'min:6', 'max:6']]);

        $user = $request->user();

        if (!$this->service->verify($user, $data['code'])) {
            return back()->withErrors(['code' => 'Invalid code. Open your authenticator app and try again.'])->with('tfa_mode', 'setup');
        }

        $codes = $this->service->confirm($user);

        return redirect()->route('user.profile.edit')
            ->with('tfa_mode', 'codes')
            ->with('tfa_codes', $codes);
    }

    /** Cancel an incomplete setup (secret was generated but never confirmed). */
    public function cancel(Request $request): RedirectResponse
    {
        if ($r = $this->denyDemo()) return $r;
        $user = $request->user();

        if (!$user->hasTwoFactorEnabled()) {
            $this->service->disable($user);
        }

        return redirect()->route('user.profile.edit');
    }

    /** Show existing recovery codes (requires password confirmation). */
    public function showRecoveryCodes(Request $request): RedirectResponse
    {
        return redirect()->route('user.profile.edit')
            ->with('tfa_mode', 'codes')
            ->with('tfa_codes', $request->user()->two_factor_recovery_codes ?? []);
    }

    /** Regenerate recovery codes (requires password confirmation). */
    public function regenerateCodes(Request $request): RedirectResponse
    {
        $codes = $this->service->regenerateRecoveryCodes($request->user());

        return redirect()->route('user.profile.edit')
            ->with('tfa_mode', 'codes')
            ->with('tfa_codes', $codes)
            ->with('success', 'New recovery codes generated. Previous codes are no longer valid.');
    }

    /** Turn 2FA off (requires password confirmation). */
    public function disable(Request $request): RedirectResponse
    {
        if ($r = $this->denyDemo()) return $r;
        $this->service->disable($request->user());

        return redirect()->route('user.profile.edit')->with('success', 'Two-factor authentication disabled.');
    }
}
