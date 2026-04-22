# Statalog — Project Details

## What is Statalog?

Statalog is a web analytics platform built with Laravel. It tracks pageviews, events, and visitor behavior for websites, giving site owners a clean privacy-focused dashboard.

The product ships in two editions:

| Edition | Audience | Repo |
|---|---|---|
| **Community** (open source) | Developers who self-host | `github.com/statalog/statalog` (public) |
| **Cloud** (SaaS) | Businesses who want a hosted solution | Private — deployed on top of community |

Both editions run the same core application. The SaaS layer is a private Laravel package that is gitignored in the open source repo and deployed separately on the cloud server.

---

## Repository Structure

```
d:/statalog.com/               ← this repo (open source, public on GitHub)
├── app/
│   ├── Http/Controllers/
│   ├── Models/
│   └── ...
├── database/
│   └── migrations/            ← core schema only (no SaaS-specific columns)
├── packages/                  ← gitignored — never committed to GitHub
│   └── cloud/                 ← private SaaS package (separate private repo)
│       ├── src/
│       │   ├── CloudServiceProvider.php
│       │   ├── Http/Controllers/
│       │   │   ├── BillingController.php
│       │   │   ├── TeamController.php
│       │   │   └── AccountController.php
│       │   ├── Models/
│       │   │   ├── Team.php
│       │   │   ├── Subscription.php
│       │   │   └── Plan.php
│       │   ├── Middleware/
│       │   │   └── SetCurrentTeamMiddleware.php
│       │   └── Scopes/
│       │       └── TeamScope.php
│       ├── routes/
│       │   └── cloud.php
│       ├── resources/views/
│       │   ├── billing/
│       │   ├── account/
│       │   └── team/
│       ├── database/migrations/
│       │   ├── 001_create_teams_table.php
│       │   ├── 002_create_team_user_table.php
│       │   └── 003_add_team_id_to_sites.php
│       └── composer.json
├── docs/
│   └── project-details.md     ← this file
├── resources/
├── routes/
├── .gitignore                 ← includes /packages/cloud
└── composer.json
```

---

## Core Schema (open source)

The community edition uses a straightforward schema. All top-level resources belong to a `user_id` — which works for single-admin self-hosted installations.

```
users
  id, name, email, password, ...

sites
  id, user_id, domain, name, timezone, ...

events
  id, site_id, name, url, referrer, device, browser, country, created_at

sessions
  id, site_id, visitor_id, started_at, ...
```

`sites.user_id` is the owner of the site. On a self-hosted install this is simply the admin user. On the SaaS cloud it doubles as the tenant identifier before the team layer takes over.

---

## SaaS Cloud Architecture

### Multi-tenancy model

Each customer on the SaaS gets one **team**. A team is the tenant boundary — it owns sites and contains users.

```
team (tenant / customer account)
  ├── owner (user who signed up)
  ├── team members (invited users with roles)
  └── sites
        └── events
              └── sessions
```

### Teams schema (added by cloud package)

```sql
teams
  id, name, owner_id (→ users.id), timestamps

team_user
  team_id (→ teams.id), user_id (→ users.id), role (owner|admin|member)
  PRIMARY KEY (team_id, user_id)

-- sites table gets one extra column:
sites.team_id (→ teams.id)
```

The cloud package adds these via its own migrations, which run on the SaaS server only.

### Team roles

| Role | Can do |
|---|---|
| `owner` | Everything — billing, delete team, manage all members |
| `admin` | Add/remove sites, invite members, view all data |
| `member` | View analytics data, no admin access |

### Current team resolution

On every authenticated request, a middleware sets the current team in the service container:

```php
app()->instance('current_team', $team);
```

A global scope on the `Site` model automatically filters all queries to the current team:

```php
Site::addGlobalScope(new TeamScope);
// Site::all() → SELECT * FROM sites WHERE team_id = {current_team_id}
```

This means all open source controllers, queries, and repositories work without any modification — the scoping is injected transparently by the cloud package.

---

## Two-Edition Flow

### Community (self-hosted)

1. User clones `github.com/statalog/statalog`
2. Runs `composer install && php artisan migrate`
3. Registers an account, adds their first site, gets the tracking script
4. No teams, no billing — single admin, full control

### Cloud (SaaS)

1. Customer signs up at `statalog.com`
2. A **team** is automatically created for them (they are the owner)
3. Customer adds sites to their team
4. Customer can invite teammates — each gets a role (admin / member)
5. Billing is per team (the team subscribes to a plan, not individual users)
6. Customer can manage multiple sites under the same account

---

## How the Cloud Package Loads

`packages/cloud` is a standard Laravel package with a service provider. It is declared as a local path repository in `composer.json`:

```json
{
    "repositories": [
        { "type": "path", "url": "packages/cloud" }
    ]
}
```

On the SaaS server, after cloning both repos:

```bash
composer require statalog/cloud:*
```

`CloudServiceProvider` is auto-discovered and boots everything: routes, migrations, middleware, model scopes, and view bindings. No changes are needed in the core application code.

On a self-hosted install, `packages/cloud` does not exist. Composer never sees it. The cloud service provider never loads. The community edition runs exactly as a standalone app.

---

## Deployment

### Self-hosted (community)

```bash
git clone https://github.com/statalog/statalog myapp
cd myapp
composer install --no-dev
cp .env.example .env
php artisan key:generate
php artisan migrate
```

### SaaS server

```bash
# Clone open source app
git clone git@github.com:statalog/statalog /var/www/statalog

# Clone private cloud package
git clone git@github.com:statalog/statalog-cloud /var/www/statalog/packages/cloud

# Install with cloud package
cd /var/www/statalog
composer require statalog/cloud:*
php artisan migrate --force
php artisan config:cache && php artisan route:cache
```

### Deploy script (run on every update)

```bash
#!/bin/bash
cd /var/www/statalog
git pull origin main

cd /var/www/statalog/packages/cloud
git pull origin main

cd /var/www/statalog
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## Edition Detection

Features exclusive to the cloud edition are gated by a config flag:

```php
// config/statalog.php
'edition' => env('STATALOG_EDITION', 'community'), // or 'cloud'
```

```php
// In views or controllers:
if (config('statalog.edition') === 'cloud') {
    // email reports, team invitations, billing portal...
}
```

```env
# .env on SaaS server
STATALOG_EDITION=cloud

# .env on self-hosted
STATALOG_EDITION=community  # or just omit it
```

---

## .gitignore entries (open source repo)

```
/packages/cloud
/packages/cloud/
```

---

## Key Design Decisions

- **One codebase, one repo** — the SaaS is not a fork. All analytics features live in the open source repo and benefit both editions.
- **Schema stays lean** — `user_id` is the only ownership field in the core schema. The cloud package adds `team_id` via a separate migration without touching core files.
- **Transparent scoping** — the `TeamScope` global scope means zero SaaS-specific code leaks into open source controllers or models.
- **Billing is fully isolated** — Stripe, plans, subscriptions, invoices live exclusively in the cloud package. Self-hosters never see billing code.
- **Teams = tenants** — one team per customer account. A team can have unlimited sites and unlimited members (subject to plan limits on the SaaS).

---

## Important requirements

- **Copyright in source files** — All php files from app/ folder must have copyright header. The code to be added is in d:/statalog.com/docs/compyright-header.php (copy the text from here and add it in all php files in header)
- **No co-authir on GitHub releases** — do not add "claude code" or other co-author when push to GitHub.
- **Never use em dash** (Unicode U+2014, the long dash character). Use a regular hyphen `-` instead, everywhere: comments, descriptions, README files, plugin.json, composer.json, Blade views, php files.
