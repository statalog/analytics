<?php
/**
 * Statalog - Open source web analytics.
 * https://statalog.com
 * @license AGPL-3.0
 */

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Mail\InvitationMail;
use App\Models\AccountUser;
use App\Models\Invitation;
use App\Models\Site;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class InvitationController extends Controller
{
    /** Send a new invitation (owner only). */
    public function store(Request $request): RedirectResponse
    {
        $owner = $request->user();

        $data = $request->validate([
            'email'      => ['required', 'email', 'max:255'],
            'role'       => ['required', 'in:admin,viewer'],
            'site_ids'   => ['nullable', 'array'],
            'site_ids.*' => ['integer'],
        ]);

        if (strtolower($data['email']) === strtolower($owner->email)) {
            return back()->withInput()->with('error', 'You cannot invite yourself.');
        }

        // Already a member?
        $existing = User::where('email', $data['email'])->first();
        if ($existing && AccountUser::where('owner_id', $owner->id)->where('user_id', $existing->id)->exists()) {
            return back()->withInput()->with('error', 'This person already has access to your account.');
        }

        // Revoke any previously pending invite for the same email + owner.
        Invitation::where('owner_id', $owner->id)
            ->where('email', $data['email'])
            ->whereNull('accepted_at')
            ->delete();

        // Resolve site restriction.
        $sitesJson = null;
        if ($data['role'] === 'viewer' && !empty($data['site_ids'])) {
            $validIds = $owner->sites->pluck('id')->toArray();
            $filtered = array_values(array_intersect($data['site_ids'], $validIds));
            if (!empty($filtered)) {
                $sitesJson = json_encode($filtered);
            }
        }

        $invitation = Invitation::create([
            'owner_id'   => $owner->id,
            'email'      => strtolower($data['email']),
            'role'       => $data['role'],
            'sites_json' => $sitesJson,
            'token'      => Invitation::generateToken(),
            'expires_at' => now()->addDays(7),
        ]);

        Mail::to($invitation->email)->send(new InvitationMail($invitation));

        return redirect()->route('user.account-users.index')
            ->with('success', 'Invitation sent to ' . $invitation->email . '.');
    }

    /** Public accept page — no auth required. */
    public function show(string $token): View|RedirectResponse
    {
        $invitation = Invitation::with('owner')->where('token', $token)->firstOrFail();

        if ($invitation->accepted_at) {
            return redirect()->route('login')
                ->with('status', 'This invitation has already been accepted. Please log in.');
        }

        if ($invitation->isExpired()) {
            return view('user.account-users.accept', ['invitation' => $invitation, 'expired' => true]);
        }

        // Record first open.
        if (!$invitation->opened_at) {
            $invitation->update(['opened_at' => now()]);
        }

        // If already logged in with the correct email, accept immediately.
        if (auth()->check() && strtolower(auth()->user()->email) === $invitation->email) {
            return view('user.account-users.accept', ['invitation' => $invitation, 'confirm' => true]);
        }

        // If the invited email already has an account, send them to login.
        $userExists = User::where('email', $invitation->email)->exists();
        if ($userExists) {
            session(['invite_token' => $token]);
            return view('user.account-users.accept', [
                'invitation'  => $invitation,
                'user_exists' => true,
            ]);
        }

        // New user — show registration form.
        return view('user.account-users.accept', [
            'invitation'  => $invitation,
            'user_exists' => false,
        ]);
    }

    /** POST — register a new user via invitation. */
    public function register(string $token): RedirectResponse
    {
        $invitation = Invitation::with('owner')->where('token', $token)->firstOrFail();

        abort_unless($invitation->isPending(), 422, 'This invitation is no longer valid.');

        // Validate registration fields.
        $data = request()->validate([
            'name'     => ['required', 'string', 'max:255'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        // Guard: email must not already exist (race condition check).
        if (User::where('email', $invitation->email)->exists()) {
            return redirect()->route('invitations.show', $token)
                ->with('error', 'An account with this email already exists. Please log in instead.');
        }

        // Create the user — email is already verified via the invitation link.
        $user = User::create([
            'name'             => $data['name'],
            'email'            => $invitation->email,
            'password'         => Hash::make($data['password']),
            'email_verified_at' => now(),
        ]);

        // Accept the invitation.
        $this->applyInvitation($invitation, $user);

        Auth::login($user);

        // Switch into the inviting account.
        session(['active_owner_id' => $invitation->owner_id]);
        cookie()->queue('statalog_account_' . $user->id, $invitation->owner_id, 60 * 24 * 30);

        return redirect()->route('user.dashboard')
            ->with('success', 'Welcome! You now have access to ' . $invitation->owner->name . '\'s account.');
    }

    /** POST — accept the invitation (logged-in user). */
    public function accept(string $token): RedirectResponse
    {
        $invitation = Invitation::with('owner')->where('token', $token)->firstOrFail();

        abort_unless($invitation->isPending(), 422, 'This invitation is no longer valid.');

        $user = auth()->user();
        abort_unless(strtolower($user->email) === $invitation->email, 403, 'This invitation was sent to a different email address.');

        $this->applyInvitation($invitation, $user);

        session(['active_owner_id' => $invitation->owner_id]);
        cookie()->queue('statalog_account_' . $user->id, $invitation->owner_id, 60 * 24 * 30);

        return redirect()->route('user.dashboard')
            ->with('success', 'Welcome! You now have access to ' . $invitation->owner->name . '\'s account.');
    }

    /** Revoke a pending invitation (owner only). */
    public function destroy(Request $request, Invitation $invitation): RedirectResponse
    {
        abort_unless($invitation->owner_id === $request->user()->id, 403);
        $invitation->delete();

        return redirect()->route('user.account-users.index')->with('success', 'Invitation revoked.');
    }

    /** Shared logic: create AccountUser record and mark invitation accepted. */
    private function applyInvitation(Invitation $invitation, User $user): void
    {
        // Accepting an invitation via email link confirms the address.
        if (!$user->email_verified_at) {
            $user->forceFill(['email_verified_at' => now()])->save();
        }

        $member = AccountUser::firstOrCreate(
            ['owner_id' => $invitation->owner_id, 'user_id' => $user->id],
            ['role' => $invitation->role],
        );

        if (!$member->wasRecentlyCreated) {
            $member->update(['role' => $invitation->role]);
        }

        if ($invitation->siteIds() !== null) {
            $validIds = Site::where('user_id', $invitation->owner_id)
                ->whereIn('id', $invitation->siteIds())
                ->pluck('id')
                ->toArray();
            $member->siteAccess()->sync($validIds);
        }

        $invitation->update(['accepted_at' => now()]);
    }
}
