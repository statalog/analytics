<?php
/**
 * Statalog - Open source web analytics.
 * https://statalog.com
 * @license AGPL-3.0
 */

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Blocks a team member with role=viewer from making write requests in
 * the account they've switched into. Owner and admins pass through.
 */
class EnsureNotViewer
{
    public function handle(Request $request, Closure $next): Response
    {
        $member = app()->bound('statalog.active_team_member')
            ? app('statalog.active_team_member')
            : null;

        if ($member && $member->isViewer()) {
            abort(403, 'Viewers cannot make changes — read-only access.');
        }

        return $next($request);
    }
}
