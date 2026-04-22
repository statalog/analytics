<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\User\LiveStatsController;
use App\Http\Controllers\User\OverviewController;
use App\Http\Controllers\User\SettingsController;
use App\Http\Controllers\User\SiteController;
use Illuminate\Support\Facades\Route;

// Root: authed users go to dashboard. Guests see the cloud landing page
// if the cloud package is installed (cloud::welcome view), otherwise
// they're redirected straight to login (self-hosted behaviour).
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('user.dashboard');
    }
    return view()->exists('cloud::welcome')
        ? view('cloud::welcome')
        : redirect()->route('login');
})->name('home');

// Shortcut redirects
Route::get('/account', fn () => redirect()->route('user.sites.index'));
Route::get('/account/dashboard', fn () => redirect()->route('user.dashboard'));

// User panel
Route::prefix('account')->name('user.')->middleware('auth')->group(function () {

    // Overview (all sites)
    Route::get('/overview', [OverviewController::class, 'index'])->name('overview');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/hostnames', [DashboardController::class, 'hostnames'])->name('dashboard.hostnames');
    Route::get('/dashboard/data', [DashboardController::class, 'data'])->name('dashboard.data');
    Route::get('/dashboard/chart', [DashboardController::class, 'chart'])->name('dashboard.chart');

    // Sites
    Route::get('/sites', [SiteController::class, 'index'])->name('sites.index');
    Route::get('/sites/create', [SiteController::class, 'create'])->name('sites.create');
    Route::post('/sites', [SiteController::class, 'store'])->name('sites.store');
    Route::get('/sites/{site}', [SiteController::class, 'show'])->name('sites.show');
    Route::put('/sites/{site}', [SiteController::class, 'update'])->name('sites.update');
    Route::delete('/sites/{site}', [SiteController::class, 'destroy'])->name('sites.destroy');

    // Live Stats
    Route::get('/live', [LiveStatsController::class, 'index'])->name('live');
    Route::get('/live/data', [LiveStatsController::class, 'data'])->name('live.data');

    // Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');

    // Profile (from Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Auth routes (published by Laravel Breeze)
require __DIR__.'/auth.php';
