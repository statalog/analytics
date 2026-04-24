<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Controllers\User\Concerns\HasDateRange;
use App\Repositories\AnalyticsRepository;
use Illuminate\Http\Request;

class VisitorLogController extends Controller
{
    use HasDateRange;

    public function __construct(protected AnalyticsRepository $analytics) {}

    public function index(Request $request)
    {
        $site = $this->getCurrentSite($request);
        if (!$site) return redirect()->route('user.sites.create');

        return view('user.visitor-log', [
            'site'        => $site,
            'breadcrumbs' => [['label' => 'Visitors']],
        ]);
    }

    public function data(Request $request)
    {
        $site = $this->getCurrentSite($request);
        if (!$site) return response()->json(['rows' => [], 'total' => 0, 'pages' => 0]);

        $perPage = 25;
        $page    = max(1, (int) $request->input('page', 1));
        $offset  = ($page - 1) * $perPage;

        $from = now()->subDays(30)->startOfDay()->format('Y-m-d H:i:s');
        $to   = now()->endOfDay()->format('Y-m-d H:i:s');

        $repo  = $this->analyticsFor($site, $request->input('hostname'));
        $total = $repo->countVisitorSessions($site->site_id, $from, $to);
        $rows  = $repo->getVisitorLog($site->site_id, $from, $to, $perPage, $offset);

        return response()->json([
            'rows'  => $rows,
            'total' => $total,
            'pages' => (int) ceil($total / $perPage),
            'page'  => $page,
        ]);
    }
}
