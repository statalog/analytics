<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Controllers\User\Concerns\HasDateRange;
use App\Repositories\AnalyticsRepository;
use App\Services\ChannelClassifierService;
use Illuminate\Http\Request;

class ChannelsController extends Controller
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

        return view('user.channels', [
            'site'        => $site,
            'breadcrumbs' => [['label' => 'Channels']],
        ]);
    }

    public function data(Request $request)
    {
        $site = $this->getCurrentSite($request);
        if (!$site) return response()->json([]);

        [$from, $to] = $this->getDateRange($request);
        $repo = $this->analyticsFor($site, $request->input('hostname'));

        $rows = $repo->getReferrerDomainStats($site->site_id, $from, $to);

        // Aggregate into channels
        $channels = [];
        foreach ($rows as $row) {
            $channel = $this->classifier->classify(
                $row['referrer_domain'] ?? '',
                $row['utm_source'] ?? ''
            );

            if (!isset($channels[$channel])) {
                $channels[$channel] = [
                    'channel'        => $channel,
                    'visits'         => 0,
                    'pageviews'      => 0,
                    'bounce_sum'     => 0,
                    'duration_sum'   => 0,
                    'sources'        => [],
                ];
            }

            $visits    = (int) ($row['visits'] ?? 0);
            $pageviews = (int) ($row['pageviews'] ?? 0);

            $channels[$channel]['visits']       += $visits;
            $channels[$channel]['pageviews']    += $pageviews;
            $channels[$channel]['bounce_sum']   += (float) ($row['bounce_rate'] ?? 0) * $visits;
            $channels[$channel]['duration_sum'] += (float) ($row['avg_duration'] ?? 0) * $visits;

            $label = $row['referrer_domain'] ?: ($row['utm_source'] ?: '(direct)');
            if ($label && count($channels[$channel]['sources']) < 50) {
                $channels[$channel]['sources'][] = [
                    'label'         => $label,
                    'visits'        => $visits,
                    'pageviews'     => $pageviews,
                    'pages_per_visit' => round($visits > 0 ? $pageviews / $visits : 0, 1),
                    'bounce_rate'   => (float) ($row['bounce_rate'] ?? 0),
                    'avg_duration'  => (int) ($row['avg_duration'] ?? 0),
                ];
            }
        }

        // Finalize: compute derived fields, sort by visits
        $result = [];
        foreach ($channels as $ch) {
            $visits = $ch['visits'];
            $result[] = [
                'channel'        => $ch['channel'],
                'visits'         => $visits,
                'pageviews'      => $ch['pageviews'],
                'pages_per_visit' => $visits > 0 ? round($ch['pageviews'] / $visits, 1) : 0,
                'bounce_rate'    => $visits > 0 ? round($ch['bounce_sum'] / $visits, 1) : 0,
                'avg_duration'   => $visits > 0 ? (int) round($ch['duration_sum'] / $visits) : 0,
                'sources'        => $ch['sources'],
            ];
        }

        usort($result, fn($a, $b) => $b['visits'] <=> $a['visits']);

        return response()->json($result);
    }
}
