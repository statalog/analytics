# Statalog — Implementation Plan

Source reference: d:/pivlu/analytics (full production app to port from)

## Key Differences: Pivlu → Statalog

| Pivlu Analytics | Statalog |
|---|---|
| `account_id` on every model (separate accounts service) | `user_id` on top-level models (self-contained) |
| `BelongsToAccount` trait (reads from app instance) | `BelongsToUser` trait (reads from auth()->id()) |
| Separate `pivlu_accounts` database for users | Standard Laravel `users` table |
| `SetAccountContext` middleware | Standard Laravel auth middleware |
| `CheckSubscription` → redirects to pivlu accounts | No subscription check in open source |
| Stripe billing in open source | Billing lives in cloud package only |
| Team management in open source | Teams live in cloud package only |
| `RedirectToAccountsLogin` middleware | Standard `auth` middleware |

---

## Phase 1 — Project Foundation

- [ ] **1.1** Update `.gitignore` — add `/packages/cloud` and `/packages/cloud/`
- [ ] **1.2** Create `packages/` and `packages/cloud/` directories (empty, gitignored)
- [ ] **1.3** Create `config/statalog.php` — edition flag, app name, version
- [ ] **1.4** Create `config/clickhouse.php` — port from Pivlu (host, port, username, password, database, timeout)
- [ ] **1.5** Update `config/database.php` — add ClickHouse connection reference, remove accounts DB
- [ ] **1.6** Update `config/cache.php` and `config/queue.php` — Redis driver
- [ ] **1.7** Install PHP packages:
  - `cybercog/laravel-clickhouse` — ClickHouse HTTP client
  - `predis/predis` — Redis
  - `maxmind-db/reader` — GeoIP
  - `donatj/phpuseragentparser` — User agent parsing
- [ ] **1.8** Update `composer.json` — add path repository for `packages/cloud`

---

## Phase 2 — Core Migrations (MySQL)

- [ ] **2.1** Default `users` table — keep Laravel default (no account_id, no Stripe columns)
- [ ] **2.2** `create_sites_table` — id, **user_id** (FK users), name, domain, site_id (unique), timezone, is_active, track_subdomains, is_public, public_token, public_password, public_sections, soft deletes, timestamps
- [ ] **2.3** `create_goals_table` — id, **user_id**, site_id, name, match_type, target_path, monetary_value, timestamps
- [ ] **2.4** `create_funnels_table` — id, **user_id**, site_id, name, timestamps
- [ ] **2.5** `create_funnel_steps_table` — id, funnel_id, label, path, step_order, timestamps
- [ ] **2.6** `create_goal_completions_table` — id, **user_id**, site_id, goal_id, visitor_id, session_id, timestamps
- [ ] **2.7** `create_settings_table` — id, **user_id**, key, value, is_encrypted, group, timestamps
- [ ] **2.8** `create_ai_insights_table` — id, **user_id**, site_id, period, period_start, period_end, content, timestamps
- [ ] **2.9** `create_ai_insight_settings_table` — id, **user_id**, site_id, enabled, report_daily, report_weekly, report_monthly, report_email, timestamps

---

## Phase 3 — ClickHouse Migrations

- [ ] **3.1** `create_pageviews_table` — port from Pivlu:
  - site_id, timestamp, session_id, visitor_id, hostname, url, path, query_string, referrer, referrer_domain, browser, browser_version, os, os_version, device_type, screen_width, screen_height, country, region, city, utm_source, utm_medium, utm_campaign, utm_content, utm_term, load_time, visit_duration, is_bounce, is_new_visitor, entry_page, exit_page
  - Engine: MergeTree, partition by month
- [ ] **3.2** `create_custom_events_table` — port from Pivlu:
  - site_id, timestamp, session_id, visitor_id, event_name, properties, url, hostname, country, device_type
  - Engine: MergeTree, partition by month

---

## Phase 4 — Traits & Shared Concerns

- [ ] **4.1** `app/Traits/BelongsToUser.php` — adapted from Pivlu's BelongsToAccount:
  - Global scope: `where('user_id', auth()->id())`
  - Auto-set `user_id` on creating
  - `withoutUserScope()` static helper
  - `scopeForUser($userId)` helper
- [ ] **4.2** `app/Http/Controllers/Concerns/HasDateRange.php` — port directly from Pivlu:
  - getDateRange(), getPreviousDateRange(), getCurrentSite(), formatDuration(), calculateTrend(), analyticsFor()

---

## Phase 5 — Models

- [ ] **5.1** `User` — add `sites()` hasMany, `settings()` hasMany, standard Laravel auth
- [ ] **5.2** `Site` — uses BelongsToUser, relationships: user(), goals(), funnels(), getTrackingSnippetAttribute()
- [ ] **5.3** `Goal` — uses BelongsToUser, matchesUrl() method
- [ ] **5.4** `Funnel` — uses BelongsToUser, steps() relationship
- [ ] **5.5** `FunnelStep` — funnel() relationship
- [ ] **5.6** `GoalCompletion` — uses BelongsToUser
- [ ] **5.7** `Setting` — uses BelongsToUser
- [ ] **5.8** `AiInsight` — uses BelongsToUser
- [ ] **5.9** `AiInsightSetting` — uses BelongsToUser

---

## Phase 6 — Services

- [ ] **6.1** `BotFilterService` — port directly (no changes needed)
- [ ] **6.2** `GeoIpService` — port directly (no changes needed)
- [ ] **6.3** `UserAgentService` — port directly (no changes needed)
- [ ] **6.4** `VisitorService` — port directly (anonymise() HMAC method)
- [ ] **6.5** `SettingsService` — adapt: scope by user_id instead of account_id
- [ ] **6.6** `AnalyticsRepository` — port from Pivlu, adaptations:
  - Remove `account_id` parameter from all method signatures (or replace with `user_id` where needed)
  - All site-level queries stay as-is (they use site_id already)
  - Keep all ClickHouse HTTP client logic intact
  - Keep all query methods: dashboard stats, chart data, top pages, traffic sources, locations, devices, browsers, OS, screen resolutions, live stats, campaigns, entry/exit, visit depth, new vs returning, time on page, funnel analysis, custom events
  - Keep insertPageview(), insertCustomEvent(), updatePageviewDuration(), purgeOlderThan()
- [ ] **6.7** `AiInsightsService` — port from Pivlu, remove credits/billing logic (goes to cloud), keep: generate(), latest(), isGloballyEnabled()

---

## Phase 7 — Jobs

- [ ] **7.1** `ProcessAnalyticsHit` — port from Pivlu, adaptations:
  - Remove account_id resolution
  - Site lookup by `site_id` field (unique identifier, not user-scoped)
  - Keep all processing: bot filter, geo, UA, visitor, bounce, new visitor detection
  - Keep ClickHouse insert logic

---

## Phase 8 — Middleware

- [ ] **8.1** `AddCorsHeaders` — port directly (for /api/collect endpoint)
- [ ] **8.2** Remove: SetAccountContext, CheckSubscription, RedirectToAccountsLogin (not needed)
- [ ] **8.3** Use standard Laravel `auth` middleware for protected routes

---

## Phase 9 — API Controllers & Routes

- [ ] **9.1** `Api/CollectController` — port from Pivlu:
  - POST/GET /api/collect — accepts hit payload, dispatches job, returns 1x1 GIF
  - Remove account_id lookup, site lookup is purely by site_id field
- [ ] **9.2** `Api/HealthController` — port directly
- [ ] **9.3** `routes/api.php` — CORS preflight, collect endpoint, health

---

## Phase 10 — Web Controllers

All controllers adapted: remove account_id, use auth()->id() or user_id directly.

- [ ] **10.1** `DashboardController` — index(), data(), chart(), hostnames()
- [ ] **10.2** `OverviewController` — index() (all sites for current user)
- [ ] **10.3** `SiteController` — full CRUD (index, create, store, show, update, destroy)
- [ ] **10.4** `LiveStatsController` — index(), data()
- [ ] **10.5** `CampaignsController` — index(), data(), drilldown()
- [ ] **10.6** `EntryExitController` — index(), data()
- [ ] **10.7** `VisitDepthController` — index(), data()
- [ ] **10.8** `NewVsReturningController` — index(), data()
- [ ] **10.9** `TimeOnPageController` — index(), data()
- [ ] **10.10** `EventController` — index(), data(), show(), showData()
- [ ] **10.11** `FunnelController` — CRUD + report()
- [ ] **10.12** `GoalController` — CRUD + report(), reportData()
- [ ] **10.13** `SettingsController` — index(), update()
- [ ] **10.14** `PublicDashboardController` — show(), unlock(), data(), chart()
- [ ] **10.15** `AiInsightsController` — index(), show(), updateSettings(), generate(), latestForDashboard()

---

## Phase 11 — Web Routes

- [ ] **11.1** `routes/web.php`:
  - Public: welcome, login, register
  - Shared dashboard: /share/{token}
  - Auth group (prefix: account/, middleware: auth):
    - Overview, Dashboard, Sites CRUD
    - Live, Campaigns, Entry/Exit, Visit Depth, New vs Returning, Time on Page
    - Events, Funnels, Goals
    - AI Insights, Settings
- [ ] **11.2** Auth routes — use Laravel Breeze (email/password only, no social)

---

## Phase 12 — Translation Files

- [ ] **12.1** `lang/en/app.php` — app name, taglines, navigation labels
- [ ] **12.2** `lang/en/analytics.php` — all dashboard labels (port from Pivlu's backend.php):
  - Page names, date ranges, stat card labels, chart labels, table columns
  - Match types, button labels, empty states
- [ ] **12.3** `lang/en/sites.php` — site management labels
- [ ] **12.4** `lang/en/auth.php` — login, register, password labels

---

## Phase 13 — Views

- [ ] **13.1** `layouts/app.blade.php` — main authenticated layout:
  - Sidebar, topbar, Chart.js CDN, Alpine.js CDN, Tailwind CSS
  - Site switcher in topbar
  - Date range picker
- [ ] **13.2** `layouts/public.blade.php` — unauthenticated layout
- [ ] **13.3** `components/sidebar.blade.php` — navigation links
- [ ] **13.4** `components/date-range-picker.blade.php`
- [ ] **13.5** `components/breadcrumb.blade.php`
- [ ] **13.6** `user/dashboard.blade.php` — main analytics dashboard
- [ ] **13.7** `user/overview.blade.php` — all sites
- [ ] **13.8** `user/live.blade.php`
- [ ] **13.9** `user/campaigns.blade.php`
- [ ] **13.10** `user/entry-exit.blade.php`
- [ ] **13.11** `user/visit-depth.blade.php`
- [ ] **13.12** `user/new-vs-returning.blade.php`
- [ ] **13.13** `user/time-on-page.blade.php`
- [ ] **13.14** `user/events/index.blade.php` + `show.blade.php`
- [ ] **13.15** `user/funnels/` — index, create, edit, report
- [ ] **13.16** `user/goals/` — index, create, edit, report
- [ ] **13.17** `user/ai-insights.blade.php` + `ai-insight-show.blade.php`
- [ ] **13.18** `user/settings.blade.php`
- [ ] **13.19** `user/sites/` — index, create, show
- [ ] **13.20** `public/dashboard.blade.php` + `public/password.blade.php`

---

## Phase 14 — Console

- [ ] **14.1** `GenerateAiInsights` command — port from Pivlu, remove credits logic

---

## Phase 15 — AppServiceProvider

- [ ] **15.1** Register singletons: GeoIpService, UserAgentService, VisitorService, BotFilterService
- [ ] **15.2** Register AnalyticsRepository as singleton

---

## Phase 16 — Cloud Package Scaffold

Create `packages/cloud/` structure (separate private repo, gitignored):

- [ ] **16.1** `packages/cloud/composer.json` — name: statalog/cloud, autoload PSR-4
- [ ] **16.2** `packages/cloud/src/CloudServiceProvider.php` — boots: routes, migrations, middleware, scopes
- [ ] **16.3** `packages/cloud/database/migrations/001_create_teams_table.php`
- [ ] **16.4** `packages/cloud/database/migrations/002_create_team_user_table.php`
- [ ] **16.5** `packages/cloud/database/migrations/003_add_team_id_to_sites.php`
- [ ] **16.6** `packages/cloud/src/Models/Team.php`
- [ ] **16.7** `packages/cloud/src/Models/Plan.php`
- [ ] **16.8** `packages/cloud/src/Scopes/TeamScope.php` — filters Site queries by team_id
- [ ] **16.9** `packages/cloud/src/Middleware/SetCurrentTeamMiddleware.php`
- [ ] **16.10** `packages/cloud/routes/cloud.php` — team management, billing routes
- [ ] **16.11** `packages/cloud/src/Http/Controllers/TeamController.php`
- [ ] **16.12** `packages/cloud/src/Http/Controllers/BillingController.php`

---

## Build Order

```
Phase 1  → Foundation & config
Phase 2  → MySQL migrations
Phase 3  → ClickHouse migrations
Phase 4  → Traits
Phase 5  → Models
Phase 6  → Services (Repository last)
Phase 7  → Job
Phase 8  → Middleware
Phase 9  → API layer (collect endpoint — core feature)
Phase 15 → AppServiceProvider (wire services)
Phase 11 → Routes skeleton
Phase 10 → Web controllers
Phase 12 → Lang files
Phase 13 → Views
Phase 14 → Console
Phase 16 → Cloud package scaffold
```

---

## What Does NOT Go in Open Source

- Stripe / Laravel Cashier
- Plans table & Plan model
- Subscriptions table
- Billing controllers & views
- Team management (teams table, team_user, team_id on sites)
- `SetCurrentTeamMiddleware`
- `TeamScope` global scope
- Any CheckSubscription logic
- Stripe webhook handling

All of the above live exclusively in `packages/cloud/`.

---

## Notes

- **Chart.js** — loaded via CDN (no npm dependency), same as Pivlu
- **GeoIP database** — MaxMind GeoLite2, stored in `storage/app/geoip/`
- **Queue** — Redis (jobs run async, same as Pivlu)
- **ClickHouse runner** — use Pivlu's `cybercog/laravel-clickhouse` package pattern
- **Session tracking** — via Laravel Cache (Redis), same TTL logic as Pivlu
- **Visitor anonymization** — HMAC-SHA256, daily-rotating, no raw IP stored
