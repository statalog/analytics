<?php
/**
 * Statalog - Open source web analytics.
 * https://statalog.com
 * @license AGPL-3.0
 */

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TeamController extends Controller
{
    public function index(Request $request): View
    {
        $owner   = $request->user();
        $members = TeamMember::where('owner_id', $owner->id)
            ->with('user', 'siteAccess')
            ->orderByDesc('created_at')
            ->get();

        return view('user.team.index', [
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
            return back()->withInput()->with('error', 'You cannot add yourself as a team member.');
        }

        $member = User::where('email', $data['email'])->first();
        if (!$member) {
            return back()->withInput()->with('error', 'No user with that email exists yet. They must sign up first before you can add them.');
        }

        if (TeamMember::where('owner_id', $owner->id)->where('user_id', $member->id)->exists()) {
            return back()->withInput()->with('error', 'This user is already a team member.');
        }

        TeamMember::create([
            'owner_id' => $owner->id,
            'user_id'  => $member->id,
            'role'     => $data['role'],
        ]);

        return redirect()->route('user.team.index')->with('success', 'Team member added.');
    }

    public function update(Request $request, TeamMember $member): RedirectResponse
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
            // Admins don't need per-site access — they see everything.
            $member->siteAccess()->detach();
        }

        return redirect()->route('user.team.index')->with('success', 'Team member updated.');
    }

    public function destroy(Request $request, TeamMember $member): RedirectResponse
    {
        abort_unless($member->owner_id === auth()->id(), 403);

        $member->siteAccess()->detach();
        $member->delete();

        return redirect()->route('user.team.index')->with('success', 'Team member removed.');
    }

    /** Switch the viewer into one of the accounts they belong to (or back to their own). */
    public function switchAccount(Request $request): RedirectResponse
    {
        $ownerId = (int) $request->input('owner_id', 0);
        $user    = $request->user();

        if ($ownerId === 0 || $ownerId === $user->id) {
            $request->session()->forget('active_owner_id');
            return redirect()->route('user.overview')->with('success', 'Switched to your own account.');
        }

        $valid = TeamMember::where('owner_id', $ownerId)->where('user_id', $user->id)->exists();
        abort_unless($valid, 403);

        $request->session()->put('active_owner_id', $ownerId);

        return redirect()->route('user.overview')->with('success', 'Switched account.');
    }
}
