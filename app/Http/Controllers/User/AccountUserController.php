<?php
/**
 * Statalog - Open source web analytics.
 * https://statalog.com
 * @license AGPL-3.0
 */

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\AccountUser;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AccountUserController extends Controller
{
    public function index(Request $request): View
    {
        $owner       = $request->user();
        $members     = AccountUser::where('owner_id', $owner->id)
            ->with('user', 'siteAccess')
            ->orderByDesc('created_at')
            ->get();
        $invitations = \App\Models\Invitation::where('owner_id', $owner->id)
            ->whereNull('accepted_at')
            ->where('expires_at', '>', now())
            ->orderByDesc('created_at')
            ->get();

        return view('user.account-users.index', [
            'owner'       => $owner,
            'members'     => $members,
            'invitations' => $invitations,
            'sites'       => $owner->sites,
        ]);
    }

    public function picker(Request $request): View|\Illuminate\Http\RedirectResponse
    {
        $user        = $request->user();
        $ownSites    = $user->sites;
        $memberships = AccountUser::where('user_id', $user->id)
            ->with(['owner.sites'])
            ->get();

        if ($memberships->isEmpty()) {
            return redirect()->route('user.dashboard');
        }

        return view('user.account-picker', compact('user', 'ownSites', 'memberships'));
    }

    public function update(Request $request, AccountUser $member): RedirectResponse
    {
        abort_unless($member->owner_id === auth()->id(), 403);

        $data = $request->validate([
            'role'    => ['required', 'in:admin,viewer'],
            'sites'   => ['nullable', 'array'],
            'sites.*' => ['integer'],
        ]);

        $member->update(['role' => $data['role']]);

        if ($data['role'] === 'viewer') {
            $ownerSiteIds = auth()->user()->sites->pluck('id')->toArray();
            $validSites   = array_values(array_intersect($data['sites'] ?? [], $ownerSiteIds));
            $member->siteAccess()->sync($validSites);
        } else {
            $member->siteAccess()->detach();
        }

        return redirect()->route('user.account-users.index')->with('success', 'Updated.');
    }

    public function destroy(Request $request, AccountUser $member): RedirectResponse
    {
        abort_unless($member->owner_id === auth()->id(), 403);
        $member->siteAccess()->detach();
        $member->delete();

        return redirect()->route('user.account-users.index')->with('success', 'Access revoked.');
    }

    public function switchAccount(Request $request): RedirectResponse
    {
        $ownerId = (int) $request->input('owner_id', 0);
        $user    = $request->user();

        if ($ownerId === 0 || $ownerId === $user->id) {
            $request->session()->forget('active_owner_id');
            $request->session()->forget('current_site_id');
            return redirect()->route('user.dashboard');
        }

        $valid = AccountUser::where('owner_id', $ownerId)->where('user_id', $user->id)->exists();
        abort_unless($valid, 403);

        $request->session()->put('active_owner_id', $ownerId);
        $request->session()->forget('current_site_id');

        return redirect()->route('user.dashboard');
    }
}
