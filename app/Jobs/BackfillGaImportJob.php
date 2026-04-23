<?php
/**
 * Statalog - Open source web analytics.
 * https://statalog.com
 * @license AGPL-3.0
 */

namespace App\Jobs;

use App\Models\GaImport;
use App\Services\GoogleAnalyticsService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable as QueueableTrait;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BackfillGaImportJob implements ShouldQueue
{
    use Queueable, QueueableTrait, InteractsWithQueue, SerializesModels;

    // Importing 12 months × 1 API call per day ~= 365 calls. Give it room.
    public int $timeout = 3600;
    public int $tries   = 1;

    public function __construct(public int $importId) {}

    public function handle(GoogleAnalyticsService $ga): void
    {
        $import = GaImport::find($this->importId);
        if (!$import) return;

        $user = $import->user;
        $site = $import->site;
        if (!$user || !$site) {
            $import->update(['status' => 'failed', 'error_message' => 'User or site no longer exists.']);
            return;
        }

        $import->update([
            'status'         => 'running',
            'started_at'     => now(),
            'total_days'     => $import->from_date->diffInDays($import->to_date) + 1,
            'processed_days' => 0,
            'error_message'  => null,
        ]);

        try {
            // 1. Daily totals — one API call per day.
            $cursor = $import->from_date->copy();
            while ($cursor->lte($import->to_date)) {
                $dateStr = $cursor->toDateString();

                $totals = $ga->dailyTotals($user, $import->ga_property_id, $dateStr);

                DB::table('ga_historical_daily')->upsert([
                    [
                        'site_id'       => $site->id,
                        'date'          => $dateStr,
                        'visitors'      => $totals['visitors'],
                        'pageviews'     => $totals['pageviews'],
                        'sessions'      => $totals['sessions'],
                        'bounce_rate'   => $totals['bounce_rate'],
                        'avg_duration'  => $totals['avg_duration'],
                        'created_at'    => now(),
                        'updated_at'    => now(),
                    ],
                ], ['site_id', 'date'], ['visitors', 'pageviews', 'sessions', 'bounce_rate', 'avg_duration', 'updated_at']);

                $import->increment('processed_days');
                $cursor->addDay();
            }

            // 2. Top pages (once, over whole period).
            $from = $import->from_date->toDateString();
            $to   = $import->to_date->toDateString();

            DB::table('ga_historical_pages')->where('site_id', $site->id)->delete();
            foreach ($ga->topPages($user, $import->ga_property_id, $from, $to, 50) as $i => $row) {
                DB::table('ga_historical_pages')->insert([
                    'site_id'    => $site->id,
                    'page_path'  => mb_substr($row['page_path'], 0, 500),
                    'pageviews'  => $row['pageviews'],
                    'rank'       => $i + 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // 3. Top sources.
            DB::table('ga_historical_sources')->where('site_id', $site->id)->delete();
            foreach ($ga->topSources($user, $import->ga_property_id, $from, $to, 20) as $i => $row) {
                DB::table('ga_historical_sources')->insert([
                    'site_id'    => $site->id,
                    'source'     => $row['source'],
                    'visitors'   => $row['visitors'],
                    'rank'       => $i + 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // 4. Top countries.
            DB::table('ga_historical_countries')->where('site_id', $site->id)->delete();
            foreach ($ga->topCountries($user, $import->ga_property_id, $from, $to, 20) as $i => $row) {
                DB::table('ga_historical_countries')->insert([
                    'site_id'    => $site->id,
                    'country'    => $row['country'],
                    'visitors'   => $row['visitors'],
                    'rank'       => $i + 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $import->update(['status' => 'completed', 'completed_at' => now()]);
        } catch (\Throwable $e) {
            Log::error('GA import failed', ['import_id' => $import->id, 'error' => $e->getMessage()]);
            $import->update([
                'status'        => 'failed',
                'error_message' => mb_substr($e->getMessage(), 0, 1000),
                'completed_at'  => now(),
            ]);
        }
    }
}
