<?php

use App\Http\Controllers\Auth\TwoFactorChallengeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicDashboardController;
use App\Http\Controllers\User\CampaignsController;
use App\Http\Controllers\User\InvitationController;
use App\Http\Controllers\User\ConfigurationController;
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\User\EntryExitController;
use App\Http\Controllers\User\BotController;
use App\Http\Controllers\User\ErrorController;
use App\Http\Controllers\User\EventController;
use App\Http\Controllers\User\FunnelController;
use App\Http\Controllers\User\GaImportController;
use App\Http\Controllers\User\GoalController;
use App\Http\Controllers\User\LiveStatsController;
use App\Http\Controllers\User\NewVsReturningController;
use App\Http\Controllers\User\SettingsController;
use App\Http\Controllers\User\SiteController;
use App\Http\Controllers\User\AccountUserController;
use App\Http\Controllers\User\DevicesController;
use App\Http\Controllers\User\LocationsController;
use App\Http\Controllers\User\PagesController;
use App\Http\Controllers\User\TimeOfDayController;
use App\Http\Controllers\User\TransitionsController;
use App\Http\Controllers\User\TimeOnPageController;
use App\Http\Controllers\User\TwoFactorController;
use App\Http\Controllers\User\VisitDepthController;
use App\Http\Controllers\User\VisitorLogController;
use App\Http\Controllers\User\PerformanceController;
use App\Http\Controllers\User\VisitorMapController;
use App\Http\Controllers\User\ChannelsController;
use App\Http\Controllers\User\SearchEnginesController;
use App\Http\Controllers\User\WebsitesController;
use App\Http\Controllers\User\SocialNetworksController;
use App\Http\Controllers\User\AiSourcesController;
use App\Http\Controllers\User\SeoToolsController;
use App\Http\Controllers\User\PdfReportController;
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

    // Overview merged into Websites; keep a redirect for bookmarks / old links.
    Route::get('/overview', fn () => redirect()->route('user.sites.index'))->name('overview');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/hostnames', [DashboardController::class, 'hostnames'])->name('dashboard.hostnames');
    Route::get('/dashboard/data', [DashboardController::class, 'data'])->name('dashboard.data');
    Route::get('/dashboard/chart', [DashboardController::class, 'chart'])->name('dashboard.chart');

    // Sites — static routes must come before {site} wildcard
    Route::get('/sites',        [SiteController::class, 'index'])->name('sites.index');
    Route::get('/sites/create', [SiteController::class, 'create'])->name('sites.create');
    Route::get('/sites/{site}', [SiteController::class, 'show'])->name('sites.show');

    // Write routes — blocked for viewers
    Route::middleware('not-viewer')->group(function () {

        Route::post('/sites',          [SiteController::class, 'store'])->name('sites.store');
        Route::put('/sites/{site}',    [SiteController::class, 'update'])->name('sites.update');
        Route::delete('/sites/{site}', [SiteController::class, 'destroy'])->name('sites.destroy');

        // Funnels — create/edit/write only
        Route::get('/funnels/create',        [FunnelController::class, 'create'])->name('funnels.create');
        Route::post('/funnels',              [FunnelController::class, 'store'])->name('funnels.store');
        Route::get('/funnels/{funnel}/edit', [FunnelController::class, 'edit'])->name('funnels.edit');
        Route::put('/funnels/{funnel}',      [FunnelController::class, 'update'])->name('funnels.update');
        Route::delete('/funnels/{funnel}',   [FunnelController::class, 'destroy'])->name('funnels.destroy');

        // Goals — create/edit/write only
        Route::get('/goals/create',        [GoalController::class, 'create'])->name('goals.create');
        Route::post('/goals',              [GoalController::class, 'store'])->name('goals.store');
        Route::get('/goals/{goal}/edit',   [GoalController::class, 'edit'])->name('goals.edit');
        Route::put('/goals/{goal}',        [GoalController::class, 'update'])->name('goals.update');
        Route::delete('/goals/{goal}',     [GoalController::class, 'destroy'])->name('goals.destroy');

    }); // end not-viewer group

    // Read-only analytics routes — accessible to all roles including viewers

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

    // Audience detail reports
    Route::get('/visitors',          [VisitorLogController::class, 'index'])->name('visitor-log');
    Route::get('/visitors/data',     [VisitorLogController::class, 'data'])->name('visitor-log.data');
    Route::get('/performance',       [PerformanceController::class, 'index'])->name('performance');
    Route::get('/performance/data',  [PerformanceController::class, 'data'])->name('performance.data');
    Route::get('/visitor-map',       [VisitorMapController::class, 'index'])->name('visitor-map');
    Route::get('/visitor-map/data',  [VisitorMapController::class, 'data'])->name('visitor-map.data');
    Route::get('/visitor-map/live',  [VisitorMapController::class, 'liveData'])->name('visitor-map.live');
    Route::get('/channels',              [ChannelsController::class, 'index'])->name('channels');
    Route::get('/channels/data',         [ChannelsController::class, 'data'])->name('channels.data');
    Route::get('/search-engines',        [SearchEnginesController::class, 'index'])->name('search-engines');
    Route::get('/search-engines/data',   [SearchEnginesController::class, 'data'])->name('search-engines.data');
    Route::get('/websites',              [WebsitesController::class, 'index'])->name('websites');
    Route::get('/websites/data',         [WebsitesController::class, 'data'])->name('websites.data');
    Route::get('/social-networks',       [SocialNetworksController::class, 'index'])->name('social-networks');
    Route::get('/social-networks/data',  [SocialNetworksController::class, 'data'])->name('social-networks.data');
    Route::get('/ai-sources',            [AiSourcesController::class, 'index'])->name('ai-sources');
    Route::get('/ai-sources/data',       [AiSourcesController::class, 'data'])->name('ai-sources.data');
    Route::get('/pages',           [PagesController::class, 'index'])->name('pages');
    Route::get('/pages/data',      [PagesController::class, 'data'])->name('pages.data');
    Route::get('/locations',       [LocationsController::class, 'index'])->name('locations');
    Route::get('/locations/data',  [LocationsController::class, 'data'])->name('locations.data');
    Route::get('/devices',         [DevicesController::class, 'index'])->name('devices');
    Route::get('/devices/data',    [DevicesController::class, 'data'])->name('devices.data');
    Route::get('/time-of-day',     [TimeOfDayController::class, 'index'])->name('time-of-day');
    Route::get('/time-of-day/data',[TimeOfDayController::class, 'data'])->name('time-of-day.data');

    // Page Transitions
    Route::get('/transitions',        [TransitionsController::class, 'index'])->name('transitions');
    Route::get('/transitions/search', [TransitionsController::class, 'search'])->name('transitions.search');
    Route::get('/transitions/data',   [TransitionsController::class, 'data'])->name('transitions.data');

    // Funnels — read-only
    Route::get('/funnels',                 [FunnelController::class, 'index'])->name('funnels.index');
    Route::get('/funnels/{funnel}/report', [FunnelController::class, 'report'])->name('funnels.report');

    // Goals — read-only
    Route::get('/goals',                     [GoalController::class, 'index'])->name('goals.index');
    Route::get('/goals/{goal}/report',       [GoalController::class, 'report'])->name('goals.report');
    Route::get('/goals/{goal}/report/data',  [GoalController::class, 'reportData'])->name('goals.report.data');

    // Custom Events
    Route::get('/events',             [EventController::class, 'index'])->name('events');
    Route::get('/events/data',        [EventController::class, 'data'])->name('events.data');
    Route::get('/events/{name}',      [EventController::class, 'show'])->name('events.show');
    Route::get('/events/{name}/data', [EventController::class, 'showData'])->name('events.show.data');

    // Tools — bots
    Route::get('/bots',      [BotController::class, 'index'])->name('bots');
    Route::get('/bots/data', [BotController::class, 'data'])->name('bots.data');

    // SEO Tools
    Route::prefix('/seo')->name('seo.')->group(function () {
        Route::get('/sitemap',                [SeoToolsController::class, 'sitemap'])->name('sitemap');
        Route::get('/sitemap/check',          [SeoToolsController::class, 'sitemapCheck'])->name('sitemap.check');
        Route::get('/robots',                 [SeoToolsController::class, 'robots'])->name('robots');
        Route::get('/robots/check',           [SeoToolsController::class, 'robotsCheck'])->name('robots.check');
        Route::get('/broken-links',       [SeoToolsController::class, 'brokenLinks'])->name('broken-links');
        Route::post('/broken-links/scan', [SeoToolsController::class, 'brokenLinksScan'])->name('broken-links.scan');
        Route::get('/redirect-checker',       [SeoToolsController::class, 'redirectChecker'])->name('redirect-checker');
        Route::get('/redirect-checker/check', [SeoToolsController::class, 'redirectCheckerCheck'])->name('redirect-checker.check');
        Route::get('/meta-tags',              [SeoToolsController::class, 'metaTags'])->name('meta-tags');
        Route::get('/meta-tags/check',        [SeoToolsController::class, 'metaTagsCheck'])->name('meta-tags.check');
    });

    // Monitoring — JS errors
    Route::get('/errors',                    [ErrorController::class, 'index'])->name('errors');
    Route::get('/errors/data',               [ErrorController::class, 'data'])->name('errors.data');
    Route::get('/errors/{fingerprint}',      [ErrorController::class, 'show'])->name('errors.show');
    Route::get('/errors/{fingerprint}/data', [ErrorController::class, 'showData'])->name('errors.show.data');

    // PDF report
    Route::get('/pdf-report',          [PdfReportController::class, 'index'])->name('pdf-report');
    Route::get('/pdf-report/generate', [PdfReportController::class, 'generate'])->name('pdf-report.generate');

    // Account switching — available to all roles
    Route::post('/account-users/switch', [AccountUserController::class, 'switchAccount'])->name('account-users.switch');
    Route::get('/account-picker',        [AccountUserController::class, 'picker'])->name('account-users.picker');

    // Configuration & management — write operations blocked for demo + viewers
    Route::middleware('not-viewer')->group(function () {
        Route::get('/configuration', [ConfigurationController::class, 'index'])->name('configuration');
        Route::get('/account-users', [AccountUserController::class, 'index'])->name('account-users.index');
        Route::put('/account-users/{member}',    [AccountUserController::class, 'update'])->name('account-users.update');
        Route::delete('/account-users/{member}', [AccountUserController::class, 'destroy'])->name('account-users.destroy');

        Route::post('/invitations',                [InvitationController::class, 'store'])->name('invitations.store');
        Route::delete('/invitations/{invitation}', [InvitationController::class, 'destroy'])->name('invitations.destroy');

        Route::get('/ga-import',                        [GaImportController::class, 'index'])->name('ga-import');
        Route::post('/ga-import/connect',               [GaImportController::class, 'connect'])->name('ga-import.connect');
        Route::get('/ga-import/callback',               [GaImportController::class, 'callback'])->name('ga-import.callback');
        Route::delete('/ga-import/disconnect',          [GaImportController::class, 'disconnect'])->name('ga-import.disconnect');
        Route::get('/ga-import/select',                 [GaImportController::class, 'selectProperty'])->name('ga-import.select');
        Route::post('/ga-import/start',                 [GaImportController::class, 'start'])->name('ga-import.start');
        Route::get('/ga-import/progress/{import}',      [GaImportController::class, 'progress'])->name('ga-import.progress');
        Route::get('/ga-import/progress/{import}/data', [GaImportController::class, 'progressData'])->name('ga-import.progress.data');
        Route::get('/ga-import/summary/{site}',         [GaImportController::class, 'summary'])->name('ga-import.summary');

        Route::get('/general', [SettingsController::class, 'index'])->name('general');
        Route::put('/general', [SettingsController::class, 'update'])->name('general.update');
    });

    // Profile (own account — viewers can edit their own profile)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Two-factor authentication
    Route::post('/two-factor/start',   [TwoFactorController::class, 'start'])->name('two-factor.start');
    Route::post('/two-factor/confirm', [TwoFactorController::class, 'confirm'])->name('two-factor.confirm');
    Route::post('/two-factor/cancel',  [TwoFactorController::class, 'cancel'])->name('two-factor.cancel');

    Route::middleware('password.confirm')->group(function () {
        Route::post('/two-factor/recovery-codes',             [TwoFactorController::class, 'showRecoveryCodes'])->name('two-factor.recovery-codes');
        Route::post('/two-factor/recovery-codes/regenerate',  [TwoFactorController::class, 'regenerateCodes'])->name('two-factor.regenerate');
        Route::delete('/two-factor', [TwoFactorController::class, 'disable'])->name('two-factor.disable');
    });
});

// Two-factor challenge (between password verification and full login).
Route::middleware('guest')->group(function () {
    Route::get('/two-factor-challenge',  [TwoFactorChallengeController::class, 'create'])->name('two-factor.challenge');
    Route::post('/two-factor-challenge', [TwoFactorChallengeController::class, 'store']);
});

// Public invitation accept page (no auth required)
Route::get('/invite/{token}',          [InvitationController::class, 'show'])->name('invitations.show');
Route::post('/invite/{token}/register',[InvitationController::class, 'register'])->name('invitations.register');
Route::post('/invite/{token}',         [InvitationController::class, 'accept'])->name('invitations.accept')->middleware('auth');

// Public shared dashboards — read-only analytics at /share/{token}
Route::get('/share/{token}',         [PublicDashboardController::class, 'show'])->name('public.dashboard');
Route::post('/share/{token}/unlock', [PublicDashboardController::class, 'unlock'])->name('public.dashboard.unlock');
Route::get('/share/{token}/data',    [PublicDashboardController::class, 'data'])->name('public.dashboard.data');
Route::get('/share/{token}/chart',   [PublicDashboardController::class, 'chart'])->name('public.dashboard.chart');

// Auth routes (published by Laravel Breeze)
require __DIR__.'/auth.php';
