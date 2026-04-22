<?php

use App\Http\Controllers\Api\CollectController;
use App\Http\Controllers\Api\HealthController;
use Illuminate\Support\Facades\Route;

Route::options('/collect', fn () => response('', 204))->middleware('cors');

Route::middleware('cors')->group(function () {
    Route::post('/collect', [CollectController::class, 'store']);
    Route::get('/collect', [CollectController::class, 'store']);
});

Route::get('/health', [HealthController::class, 'index']);
