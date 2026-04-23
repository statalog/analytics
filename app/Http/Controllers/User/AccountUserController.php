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
        $owner   = $request->user();
        $members = AccountUser::where('owner_id', $owner->id)
            ->with('user', 'siteAccess')
            ->orderByDesc('created_at')
            ->get();

        return view('user.account-users.index', [
            'owner'   => $owner,
            'members' => $members,
            'sites'   => $owner->sites,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'role'  => ['required', 'in:admin,viewer'],
        ]);

        $owner = $request->user();

        if ($data['email'] === $owner->email) {
            return back()->withInput()->with('error', 'You cannot add yourself.');
        }

        $member = User::where('email', $data['email'])->first();
        if (!$member) {
            return back()->withInput()->with('error', 'No user with that email. They must sign up first.');
        }

        if (AccountUser::where('owner_id', $owner->id)->where('user_id', $member->id)->exists()) {
            return back()->withInput()->with('error', 'This user already has access.');
        }

        AccountUser::create([
            'owner_id' => $owner->id,
            'user_id'  => $member->id,
            'role'     => $data['role'],
        ]);

        return redirect()->route('user.account-users.index')->with('success', 'Access granted.');
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
            return redirect()->route('user.overview')->with('success', 'Switched to your own account.');
        }

        $valid = AccountUser::where('owner_id', $ownerId)->where('user_id', $user->id)->exists();
        abort_unless($valid, 403);

        $request->session()->put('active_owner_id', $ownerId);

        return redirect()->route('user.overview')->with('success', 'Switched account.');
    }
}
