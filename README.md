<h1 align="center">Statalog</h1>

<p align="center">
  <strong>Privacy-first, self-hosted web analytics.</strong><br>
  No cookies. No cross-site tracking. No personal data stored. GDPR-compliant out of the box.
</p>

<p align="center">
  <a href="https://github.com/statalog/analytics/releases"><img src="https://img.shields.io/github/v/release/statalog/analytics?color=0e7dd5" alt="Latest release"></a>
  <a href="LICENSE"><img src="https://img.shields.io/badge/license-AGPL--3.0-blue.svg" alt="License"></a>
  <img src="https://img.shields.io/badge/PHP-8.3+-777BB4" alt="PHP 8.3+">
  <img src="https://img.shields.io/badge/Laravel-13-FF2D20" alt="Laravel 13">
</p>

---

Statalog is an open-source alternative to Google Analytics, Matomo and Plausible. Drop a single 2KB script on your site, keep the data on your own server, and get a fast, modern dashboard with everything you need to understand your traffic — nothing you don't.

## Features

- **Real-time dashboard** — unique visitors, sessions, pageviews, bounce rate and average visit duration, with period-over-period comparison
- **Top pages, traffic sources, devices, browsers, operating systems, screen resolutions, countries and cities**
- **Live Stats** — see who is on your site right now, which pages they are viewing, and how traffic is trending minute by minute
- **Multi-site support** — track as many websites as you want from a single installation, switchable from the sidebar
- **Lightweight tracking script** — under 2KB, cookieless, honors Do Not Track
- **Anonymous visitor identification** — HMAC-SHA256 with a daily rotating salt. No raw IPs stored. Visitors cannot be tracked across days.
- **Privacy controls** — exclude internal IPs, hide city-level data, per-visitor localStorage opt-out
- **Dark mode**, responsive UI, keyboard-friendly
- **Admin created via a single Artisan command** — no public signup on self-hosted installs

## Requirements

- PHP 8.3+
- Composer 2
- MySQL 8 or MariaDB 10.11+
- [ClickHouse](https://clickhouse.com/) — stores the raw hit data
- Redis — queue and cache
- Node.js 20+ (only for building front-end assets)

## Quick start

```bash
# 1. Clone
git clone https://github.com/statalog/analytics.git statalog
cd statalog

# 2. Install dependencies
composer install --no-dev
cp .env.example .env
php artisan key:generate

# 3. Configure .env — set DB_*, CLICKHOUSE_* and REDIS_* connection details

# 4. Run migrations (MySQL + ClickHouse)
php artisan migrate

# 5. Create your admin user
php artisan statalog:create-admin

# 6. Serve it
php artisan serve
```

Visit `http://localhost:8000/login`, sign in, add your first website, and copy the tracking snippet into the `<head>` of the site you want to track.

## Tracking script

Paste the generated snippet into the `<head>` of every page you want to track:

```html
<script defer data-site-id="SA-XXXXXXXXXXXXX" src="https://your-statalog-domain.com/js/tracker.js"></script>
```

That's it. Pageviews start flowing within seconds.

## Tech stack

PHP 8.3 · Laravel 13 · Bootstrap 5 · Chart.js · ClickHouse · Redis · MySQL

## Roadmap

Statalog 1.0 focuses on the core real-time dashboard. More features are rolling out in subsequent releases — campaigns and UTM drill-down, goal tracking, conversion funnels, custom events, public shared dashboards and more. Stars and feedback on the [issues page](https://github.com/statalog/analytics/issues) help shape priorities.

## Cloud hosting

Prefer a managed instance with one-click setup, automatic upgrades, SSL and backups? [statalog.com](https://statalog.com) offers hosted Statalog with a free tier.

## Contributing

Bug reports and pull requests are welcome. Please open an issue first for anything larger than a small fix so we can discuss the approach.

## License

Statalog is released under the [GNU AGPL-3.0](LICENSE) license.
