<?php
/**
 * Statalog - Open source web analytics.
 * https://statalog.com
 * @license AGPL-3.0
 */

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Controllers\User\Concerns\HasDateRange;
use App\Repositories\AnalyticsRepository;
use Illuminate\Http\Request;

class TimeOfDayController extends Controller
{
    use HasDateRange;

    public function __construct(protected AnalyticsRepository $analytics) {}

    public function index(Request $request)
    {
        $site = $this->getCurrentSite($request);
        if (!$site) return redirect()->route('user.sites.create');

        return view('user.time-of-day', [
            'site'        => $site,
            'breadcrumbs' => [['label' => 'Time of Day']],
        ]);
    }

    public function data(Request $request)
    {
        $site = $this->getCurrentSite($request);
        if (!$site) return response()->json([]);

        [$from, $to] = $this->getDateRange($request);

        return response()->json(
            $this->analyticsFor($site, $request->input('hostname'))
                ->getHourlyHeatmap($site->site_id, $from, $to, $site->timezone ?? 'UTC')
        );
    }
}
