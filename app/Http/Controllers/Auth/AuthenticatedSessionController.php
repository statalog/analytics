<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\AccountUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->ensureIsNotRateLimited();

        $credentials = $request->only('email', 'password');

        // Verify credentials without establishing a session — this lets us
        // route users with 2FA enabled to the challenge screen first.
        if (!Auth::validate($credentials)) {
            \Illuminate\Support\Facades\RateLimiter::hit($request->throttleKey());
            throw \Illuminate\Validation\ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        \Illuminate\Support\Facades\RateLimiter::clear($request->throttleKey());

        $user = Auth::getProvider()->retrieveByCredentials($credentials);

        if ($user->hasTwoFactorEnabled()) {
            $request->session()->put('login.id', $user->getAuthIdentifier());
            $request->session()->put('login.remember', $request->boolean('remember'));
            return redirect()->route('two-factor.challenge');
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        return $this->afterLogin($request, $user);
    }

    protected function afterLogin(Request $request, $user): RedirectResponse
    {
        // If arriving from an invitation link, return there.
        if ($token = $request->session()->pull('invite_token')) {
            return redirect()->route('invitations.show', $token);
        }

        // Restore last-used account from cookie.
        $cookieKey = 'statalog_account_' . $user->id;
        $savedOwner = (int) ($request->cookie($cookieKey) ?? 0);
        if ($savedOwner && $savedOwner !== $user->id) {
            $valid = AccountUser::where('owner_id', $savedOwner)->where('user_id', $user->id)->exists();
            if ($valid) {
                $request->session()->put('active_owner_id', $savedOwner);
                return redirect()->intended(route('user.dashboard', absolute: false));
            }
        }

        // No own sites — check memberships.
        if ($user->sites()->count() === 0) {
            $memberships = AccountUser::where('user_id', $user->id)->get();

            if ($memberships->count() === 1) {
                $request->session()->put('active_owner_id', $memberships->first()->owner_id);
                return redirect()->intended(route('user.dashboard', absolute: false));
            }

            if ($memberships->count() > 1) {
                return redirect()->route('user.account-users.picker');
            }
        }

        return redirect()->intended(route('user.dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
