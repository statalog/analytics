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

class DevicesController extends Controller
{
    use HasDateRange;

    public function __construct(protected AnalyticsRepository $analytics) {}

    public function index(Request $request)
    {
        $site = $this->getCurrentSite($request);
        if (!$site) return redirect()->route('user.sites.create');

        return view('user.devices', [
            'site'        => $site,
            'breadcrumbs' => [['label' => 'Devices']],
        ]);
    }

    public function data(Request $request)
    {
        $site = $this->getCurrentSite($request);
        if (!$site) return response()->json([]);

        [$from, $to] = $this->getDateRange($request);
        $repo        = $this->analyticsFor($site, $request->input('hostname'));

        return response()->json([
            'devices'     => $repo->getDevices($site->site_id, $from, $to),
            'browsers'    => $repo->getBrowsers($site->site_id, $from, $to, 20),
            'os'          => $repo->getOperatingSystems($site->site_id, $from, $to, 20),
            'resolutions' => $repo->getScreenResolutions($site->site_id, $from, $to, 20),
        ]);
    }
}
