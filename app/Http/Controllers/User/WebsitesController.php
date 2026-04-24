<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Controllers\User\Concerns\HasDateRange;
use App\Repositories\AnalyticsRepository;
use App\Services\ChannelClassifierService;
use Illuminate\Http\Request;

class WebsitesController extends Controller
{
    use HasDateRange;

    public function __construct(
        protected AnalyticsRepository $analytics,
        protected ChannelClassifierService $classifier,
    ) {}

    public function index(Request $request)
    {
        $site = $this->getCurrentSite($request);
        if (!$site) return redirect()->route('user.sites.create');

        return view('user.websites', [
            'site'        => $site,
            'breadcrumbs' => [['label' => 'Websites']],
        ]);
    }

    public function data(Request $request)
    {
        $site = $this->getCurrentSite($request);
        if (!$site) return response()->json([]);

        [$from, $to] = $this->getDateRange($request);
        $repo = $this->analyticsFor($site, $request->input('hostname'));

        $rows = $repo->getReferrerDomainStats($site->site_id, $from, $to);

        $result = [];
        foreach ($rows as $row) {
            $domain = $row['referrer_domain'] ?? '';
            if ($row['utm_source'] ?? '') continue;
            if ($this->classifier->classify($domain, '') !== ChannelClassifierService::WEBSITES) continue;

            $result[] = [
                'domain'          => $domain,
                'visits'          => (int) ($row['visits'] ?? 0),
                'pageviews'       => (int) ($row['pageviews'] ?? 0),
                'pages_per_visit' => (float) ($row['pages_per_visit'] ?? 0),
                'bounce_rate'     => (float) ($row['bounce_rate'] ?? 0),
                'avg_duration'    => (int) ($row['avg_duration'] ?? 0),
            ];
        }

        usort($result, fn($a, $b) => $b['visits'] <=> $a['visits']);

        return response()->json($result);
    }
}
