<?php

use App\Http\Controllers\Auth\TwoFactorChallengeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicDashboardController;
use App\Http\Controllers\User\CampaignsController;
use App\Http\Controllers\User\ConfigurationController;
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\User\EntryExitController;
use App\Http\Controllers\User\EventController;
use App\Http\Controllers\User\FunnelController;
use App\Http\Controllers\User\GaImportController;
use App\Http\Controllers\User\GoalController;
use App\Http\Controllers\User\LiveStatsController;
use App\Http\Controllers\User\NewVsReturningController;
use App\Http\Controllers\User\OverviewController;
use App\Http\Controllers\User\SettingsController;
use App\Http\Controllers\User\SiteController;
use App\Http\Controllers\User\AccountUserController;
use App\Http\Controllers\User\TimeOnPageController;
use App\Http\Controllers\User\TwoFactorController;
use App\Http\Controllers\User\VisitDepthController;
use Illuminate\Support\Facades\Route;

// Root: authed users go to dashboard. Guests see the cloud landing page
// if the cloud package is installed (cloud::welcome view), otherwise
// they're redirected straight to login (self-hosted behaviour).
Route::get('/', function () {
    // Cloud: always render the marketing landing — authed users can still
    // visit their own site. The landing's header links take them to the
    // dashboard or login as appropriate.
    if (view()->exists('cloud::welcome')) {
        return view('cloud::welcome');
    }

    // Self-hosted: landing page is out of scope, go straight to the app.
    return auth()->check()
        ? redirect()->route('user.dashboard')
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

    // Campaigns
    Route::get('/campaigns', [CampaignsController::class, 'index'])->name('campaigns');
    Route::get('/campaigns/data', [CampaignsController::class, 'data'])->name('campaigns.data');
    Route::get('/campaigns/drilldown', [CampaignsController::class, 'drilldown'])->name('campaigns.drilldown');

    // Entry & Exit Pages
    Route::get('/entry-exit', [EntryExitController::class, 'index'])->name('entry-exit');
    Route::get('/entry-exit/data', [EntryExitController::class, 'data'])->name('entry-exit.data');

    // Visit Depth
    Route::get('/visit-depth', [VisitDepthController::class, 'index'])->name('visit-depth');
    Route::get('/visit-depth/data', [VisitDepthController::class, 'data'])->name('visit-depth.data');

    // New vs Returning
    Route::get('/new-vs-returning', [NewVsReturningController::class, 'index'])->name('new-vs-returning');
    Route::get('/new-vs-returning/data', [NewVsReturningController::class, 'data'])->name('new-vs-returning.data');

    // Time on Page
    Route::get('/time-on-page', [TimeOnPageController::class, 'index'])->name('time-on-page');
    Route::get('/time-on-page/data', [TimeOnPageController::class, 'data'])->name('time-on-page.data');

    // Funnels
    Route::get('/funnels',                  [FunnelController::class, 'index'])->name('funnels.index');
    Route::get('/funnels/create',           [FunnelController::class, 'create'])->name('funnels.create');
    Route::post('/funnels',                 [FunnelController::class, 'store'])->name('funnels.store');
    Route::get('/funnels/{funnel}/edit',    [FunnelController::class, 'edit'])->name('funnels.edit');
    Route::put('/funnels/{funnel}',         [FunnelController::class, 'update'])->name('funnels.update');
    Route::delete('/funnels/{funnel}',      [FunnelController::class, 'destroy'])->name('funnels.destroy');
    Route::get('/funnels/{funnel}/report',  [FunnelController::class, 'report'])->name('funnels.report');

    // Goals
    Route::get('/goals',                      [GoalController::class, 'index'])->name('goals.index');
    Route::get('/goals/create',               [GoalController::class, 'create'])->name('goals.create');
    Route::post('/goals',                     [GoalController::class, 'store'])->name('goals.store');
    Route::get('/goals/{goal}/edit',          [GoalController::class, 'edit'])->name('goals.edit');
    Route::put('/goals/{goal}',               [GoalController::class, 'update'])->name('goals.update');
    Route::delete('/goals/{goal}',            [GoalController::class, 'destroy'])->name('goals.destroy');
    Route::get('/goals/{goal}/report',        [GoalController::class, 'report'])->name('goals.report');
    Route::get('/goals/{goal}/report/data',   [GoalController::class, 'reportData'])->name('goals.report.data');

    // Custom Events
    Route::get('/events',               [EventController::class, 'index'])->name('events');
    Route::get('/events/data',          [EventController::class, 'data'])->name('events.data');
    Route::get('/events/{name}',        [EventController::class, 'show'])->name('events.show');
    Route::get('/events/{name}/data',   [EventController::class, 'showData'])->name('events.show.data');

    // Configuration hub (integrations, external connections)
    Route::get('/configuration', [ConfigurationController::class, 'index'])->name('configuration');

    // Team members
    Route::get('/account-users',             [AccountUserController::class, 'index'])->name('account-users.index');
    Route::post('/account-users',            [AccountUserController::class, 'store'])->name('account-users.store');
    Route::put('/account-users/{member}',    [AccountUserController::class, 'update'])->name('account-users.update');
    Route::delete('/account-users/{member}', [AccountUserController::class, 'destroy'])->name('account-users.destroy');
    Route::post('/account-users/switch',     [AccountUserController::class, 'switchAccount'])->name('account-users.switch');

    // Google Analytics import (reachable from Configuration)
    Route::get('/ga-import',                        [GaImportController::class, 'index'])->name('ga-import');
    Route::post('/ga-import/connect',               [GaImportController::class, 'connect'])->name('ga-import.connect');
    Route::get('/ga-import/callback',               [GaImportController::class, 'callback'])->name('ga-import.callback');
    Route::delete('/ga-import/disconnect',          [GaImportController::class, 'disconnect'])->name('ga-import.disconnect');
    Route::get('/ga-import/select',                 [GaImportController::class, 'selectProperty'])->name('ga-import.select');
    Route::post('/ga-import/start',                 [GaImportController::class, 'start'])->name('ga-import.start');
    Route::get('/ga-import/progress/{import}',      [GaImportController::class, 'progress'])->name('ga-import.progress');
    Route::get('/ga-import/progress/{import}/data', [GaImportController::class, 'progressData'])->name('ga-import.progress.data');
    Route::get('/ga-import/summary/{site}',         [GaImportController::class, 'summary'])->name('ga-import.summary');

    // Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');

    // Profile (from Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Two-factor authentication (profile page actions)
    Route::post('/two-factor/start',   [TwoFactorController::class, 'start'])->name('two-factor.start');
    Route::post('/two-factor/confirm', [TwoFactorController::class, 'confirm'])->name('two-factor.confirm');
    Route::post('/two-factor/cancel',  [TwoFactorController::class, 'cancel'])->name('two-factor.cancel');

    Route::middleware('password.confirm')->group(function () {
        Route::post('/two-factor/recovery-codes',    [TwoFactorController::class, 'showRecoveryCodes'])->name('two-factor.recovery-codes');
        Route::post('/two-factor/recovery-codes/regenerate', [TwoFactorController::class, 'regenerateCodes'])->name('two-factor.regenerate');
        Route::delete('/two-factor', [TwoFactorController::class, 'disable'])->name('two-factor.disable');
    });
});

// Two-factor challenge (between password verification and full login).
Route::middleware('guest')->group(function () {
    Route::get('/two-factor-challenge',  [TwoFactorChallengeController::class, 'create'])->name('two-factor.challenge');
    Route::post('/two-factor-challenge', [TwoFactorChallengeController::class, 'store']);
});

// Public shared dashboards — read-only analytics at /share/{token}
Route::get('/share/{token}',         [PublicDashboardController::class, 'show'])->name('public.dashboard');
Route::post('/share/{token}/unlock', [PublicDashboardController::class, 'unlock'])->name('public.dashboard.unlock');
Route::get('/share/{token}/data',    [PublicDashboardController::class, 'data'])->name('public.dashboard.data');
Route::get('/share/{token}/chart',   [PublicDashboardController::class, 'chart'])->name('public.dashboard.chart');

// Auth routes (published by Laravel Breeze)
require __DIR__.'/auth.php';
