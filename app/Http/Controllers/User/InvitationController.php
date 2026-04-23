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
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class InvitationController extends Controller
{
    /** Send a new invitation (owner only). */
    public function store(Request $request): RedirectResponse
    {
        $owner = $request->user();

        $data = $request->validate([
            'email'    => ['required', 'email', 'max:255'],
            'role'     => ['required', 'in:admin,viewer'],
            'site_ids' => ['nullable', 'array'],
            'site_ids.*' => ['integer'],
        ]);

        if (strtolower($data['email']) === strtolower($owner->email)) {
            return back()->withInput()->with('error', 'You cannot invite yourself.');
        }

        // Already a member?
        $existing = \App\Models\User::where('email', $data['email'])->first();
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
            return redirect()->route('user.dashboard')
                ->with('status', 'This invitation has already been accepted.');
        }

        if ($invitation->isExpired()) {
            return view('user.account-users.accept', ['invitation' => $invitation, 'expired' => true]);
        }

        // If not logged in, store the token and redirect to login.
        if (!auth()->check()) {
            session(['invite_token' => $token]);
            return redirect()->route('login')
                ->with('status', 'Please sign in (or create an account) to accept the invitation.');
        }

        $user = auth()->user();

        // Logged in but wrong email.
        if (strtolower($user->email) !== $invitation->email) {
            return view('user.account-users.accept', [
                'invitation'  => $invitation,
                'wrong_email' => true,
                'user_email'  => $user->email,
            ]);
        }

        return view('user.account-users.accept', ['invitation' => $invitation]);
    }

    /** POST — accept the invitation. */
    public function accept(string $token): RedirectResponse
    {
        $invitation = Invitation::with('owner')->where('token', $token)->firstOrFail();

        abort_unless($invitation->isPending(), 422, 'This invitation is no longer valid.');

        $user = auth()->user();
        abort_unless(strtolower($user->email) === $invitation->email, 403, 'This invitation was sent to a different email address.');

        // Already a member? Just accept silently.
        $member = AccountUser::firstOrCreate(
            ['owner_id' => $invitation->owner_id, 'user_id' => $user->id],
            ['role' => $invitation->role],
        );

        if ($member->wasRecentlyCreated) {
            $member->update(['role' => $invitation->role]);

            // Apply per-site restriction if specified.
            if ($invitation->siteIds() !== null) {
                $validIds = Site::where('user_id', $invitation->owner_id)
                    ->whereIn('id', $invitation->siteIds())
                    ->pluck('id')
                    ->toArray();
                $member->siteAccess()->sync($validIds);
            }
        }

        $invitation->update(['accepted_at' => now()]);

        // Switch into the new account immediately.
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
}
