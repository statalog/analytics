<?php
/**
 * Statalog - Open source web analytics.
 * https://statalog.com
 * @license AGPL-3.0
 */

namespace App\Http\Middleware;

use App\Models\AccountUser;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Resolves the "active account" for the current request.
 *
 * - Default: viewer sees their own data — no container binding, BelongsToUser
 *   falls back to auth()->id().
 * - If the user has switched into an owner's account via the topbar switcher,
 *   we validate they have an AccountUser record for that owner and bind the
 *   owner id into the container so BelongsToUser scopes to the owner.
 */
class ResolveActiveAccount
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (!$user) {
            return $next($request);
        }

        $activeOwnerId = (int) $request->session()->get('active_owner_id', 0);

        // Viewing own data — nothing to do.
        if ($activeOwnerId === 0 || $activeOwnerId === $user->id) {
            return $next($request);
        }

        // Validate membership. If revoked, silently fall back to own account.
        $member = AccountUser::where('owner_id', $activeOwnerId)
            ->where('user_id', $user->id)
            ->first();

        if (!$member) {
            $request->session()->forget('active_owner_id');
            return $next($request);
        }

        app()->instance('statalog.active_owner_id', $activeOwnerId);
        app()->instance('statalog.active_account_user', $member);
        $request->attributes->set('active_owner_id', $activeOwnerId);
        $request->attributes->set('active_account_user', $member);

        return $next($request);
    }
}
