<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Controllers\User\Concerns\HasDateRange;
use App\Repositories\AnalyticsRepository;
use Illuminate\Http\Request;

class PerformanceController extends Controller
{
    use HasDateRange;

    public function __construct(protected AnalyticsRepository $analytics) {}

    public function index(Request $request)
    {
        $site = $this->getCurrentSite($request);
        if (!$site) return redirect()->route('user.sites.create');

        return view('user.performance', [
            'site'        => $site,
            'breadcrumbs' => [['label' => 'Performance']],
        ]);
    }

    public function data(Request $request)
    {
        $site = $this->getCurrentSite($request);
        if (!$site) return response()->json([]);

        [$from, $to] = $this->getDateRange($request);
        $repo = $this->analyticsFor($site, $request->input('hostname'));

        return response()->json([
            'overview' => $repo->getPerformanceOverview($site->site_id, $from, $to),
            'chart'    => $repo->getPerformanceOverTime($site->site_id, $from, $to, $site->timezone ?? 'UTC'),
        ]);
    }
}
