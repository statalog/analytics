<?php
/**
 * Statalog - Open source web analytics.
 * https://statalog.com
 * @license AGPL-3.0
 */

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\TwoFactorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TwoFactorChallengeController extends Controller
{
    public function __construct(protected TwoFactorService $service) {}

    public function create(Request $request): View|RedirectResponse
    {
        if (!$request->session()->has('login.id')) {
            return redirect()->route('login');
        }

        return view('auth.two-factor-challenge');
    }

    public function store(Request $request): RedirectResponse
    {
        $userId = $request->session()->get('login.id');
        if (!$userId) {
            return redirect()->route('login');
        }

        $user = User::find($userId);
        if (!$user || !$user->hasTwoFactorEnabled()) {
            return redirect()->route('login');
        }

        $code         = trim((string) $request->input('code', ''));
        $recoveryCode = trim((string) $request->input('recovery_code', ''));

        if ($code !== '' && $this->service->verify($user, $code)) {
            return $this->completeLogin($request, $user);
        }

        if ($recoveryCode !== '' && $this->service->useRecoveryCode($user, $recoveryCode)) {
            return $this->completeLogin($request, $user);
        }

        return back()->withErrors(['code' => 'The provided code was invalid.']);
    }

    private function completeLogin(Request $request, User $user): RedirectResponse
    {
        $remember = (bool) $request->session()->pull('login.remember', false);

        Auth::login($user, $remember);
        $request->session()->forget('login.id');
        $request->session()->regenerate();

        return redirect()->intended(route('user.dashboard', absolute: false));
    }
}
