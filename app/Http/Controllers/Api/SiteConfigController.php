<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Returns the runtime config the static tracker.js needs for cloud features
 * (heatmaps, etc.). OSS-only installs return an empty config — the tracker
 * silently no-ops anything that depends on it.
 *
 * The cloud package overrides this controller to serve real data.
 */
class SiteConfigController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        return response()->json([
            'heatmaps' => ['patterns' => []],
        ])->header('Access-Control-Allow-Origin', '*')
          ->header('Cache-Control', 'public, max-age=300');
    }
}
