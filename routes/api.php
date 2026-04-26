<?php

use App\Http\Controllers\Api\CollectController;
use App\Http\Controllers\Api\HealthController;
use App\Http\Controllers\Api\PixelController;
use App\Http\Controllers\Api\SiteConfigController;
use App\Http\Controllers\Api\V1\StatsController;
use Illuminate\Support\Facades\Route;

Route::options('/collect', fn () => response('', 204))->middleware('cors');

Route::middleware('cors')->group(function () {
    Route::post('/collect', [CollectController::class, 'store']);
    Route::get('/collect', [CollectController::class, 'store']);
    Route::get('/site-config', [SiteConfigController::class, 'show']);
});

Route::get('/pixel', [PixelController::class, 'track']);

Route::get('/health', [HealthController::class, 'index']);

/*
|--------------------------------------------------------------------------
| REST API v1
|--------------------------------------------------------------------------
| Protected by STATALOG_API_KEY (.env). Cloud overrides ApiAuthenticate
| with per-user DB-backed key management.
| All endpoints require ?site_id=ST-XXXXX and optional ?from=&to= (Y-m-d).
*/
Route::prefix('v1')->middleware(['cors', 'api.auth'])->group(function () {
    Route::get('/stats',            [StatsController::class, 'summary']);
    Route::get('/stats/timeseries', [StatsController::class, 'timeseries']);
    Route::get('/pages',            [StatsController::class, 'pages']);
    Route::get('/referrers',        [StatsController::class, 'referrers']);
    Route::get('/channels',         [StatsController::class, 'channels']);
    Route::get('/locations',        [StatsController::class, 'locations']);
    Route::get('/devices',          [StatsController::class, 'devices']);
    Route::get('/events',           [StatsController::class, 'events']);
    Route::get('/campaigns',        [StatsController::class, 'campaigns']);
    Route::get('/live',             [StatsController::class, 'live']);
});
