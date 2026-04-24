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

class TransitionsController extends Controller
{
    use HasDateRange;

    public function __construct(protected AnalyticsRepository $analytics) {}

    public function index(Request $request)
    {
        $site = $this->getCurrentSite($request);
        if (!$site) return redirect()->route('user.sites.create');

        return view('user.transitions', ['site' => $site]);
    }

    public function search(Request $request)
    {
        $site = $this->getCurrentSite($request);
        if (!$site) return response()->json([]);

        $q = trim($request->input('q', ''));
        if ($q === '') return response()->json([]);

        $urls = $this->analyticsFor($site)->searchPageUrls($site->site_id, $q);

        return response()->json(array_map(fn($url) => ['value' => $url, 'text' => preg_replace('#^https?://#', '', $url)], $urls));
    }

    public function data(Request $request)
    {
        $site = $this->getCurrentSite($request);
        if (!$site) return response()->json([]);

        [$from, $to] = $this->getDateRange($request);
        $url = trim($request->input('url', ''));

        if ($url === '') {
            return response()->json(['error' => 'No URL specified.']);
        }

        return response()->json(
            $this->analyticsFor($site)
                ->getPageTransitions($site->site_id, $from, $to, $url)
        );
    }
}
