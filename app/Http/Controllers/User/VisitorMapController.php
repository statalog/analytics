<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Controllers\User\Concerns\HasDateRange;
use App\Repositories\AnalyticsRepository;
use Illuminate\Http\Request;

class VisitorMapController extends Controller
{
    use HasDateRange;

    public function __construct(protected AnalyticsRepository $analytics) {}

    public function index(Request $request)
    {
        $site = $this->getCurrentSite($request);
        if (!$site) return redirect()->route('user.sites.create');

        return view('user.visitor-map', [
            'site'        => $site,
            'breadcrumbs' => [['label' => 'Visitor Map']],
        ]);
    }

    public function data(Request $request)
    {
        $site = $this->getCurrentSite($request);
        if (!$site) return response()->json([]);

        [$from, $to] = $this->getDateRange($request);
        $repo = $this->analyticsFor($site, $request->input('hostname'));

        return response()->json([
            'points' => $repo->getVisitorMapPoints($site->site_id, $from, $to),
        ]);
    }

    public function liveData(Request $request)
    {
        $site = $this->getCurrentSite($request);
        if (!$site) return response()->json([]);

        $minutes = (int) $request->input('minutes', 30);
        $minutes = max(5, min(60, $minutes));

        $repo = $this->analyticsFor($site);

        return response()->json([
            'points' => $repo->getLiveVisitorMap($site->site_id, $minutes),
        ]);
    }
}
