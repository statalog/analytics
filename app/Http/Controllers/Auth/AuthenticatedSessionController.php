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
        // If arriving from an invitation link, return there first.
        if ($token = $request->session()->pull('invite_token')) {
            return redirect()->route('invitations.show', $token);
        }

        // Always show account picker if the user is a member of any other account.
        $memberships = AccountUser::where('user_id', $user->id)->with('owner')->get();

        if ($memberships->isNotEmpty()) {
            return redirect()->route('user.account-users.picker');
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
