# Statalog — Self-Hosted Documentation

This guide covers how to manage a self-hosted Statalog installation. For the SaaS version, see [statalog.com](https://statalog.com).

---

## Creating an Admin User

Self-hosted Statalog does **not** expose a public `/register` page. Registration is disabled by design — you install the app for yourself (or your team) and create users from the command line.

### Interactive mode

Run the command from your Statalog install directory:

```bash
php artisan statalog:create-admin
```

You'll be prompted for:

1. **Full name**
2. **Email address** (entered twice for confirmation)
3. **Password** (entered twice for confirmation, minimum 8 characters)

Example session:

```
$ php artisan statalog:create-admin
Full name:
 > Jane Doe

Email address:
 > jane@example.com

Confirm email:
 > jane@example.com

Password (min 8 chars):
 > ********

Confirm password:
 > ********

Admin user created: jane@example.com (ID: 1)
You can now log in at https://analytics.example.com/login
```

### Non-interactive mode (Docker, CI, provisioning)

Pass all values as options — no prompts will appear:

```bash
php artisan statalog:create-admin \
  --name="Jane Doe" \
  --email="jane@example.com" \
  --password="a-strong-password"
```

This is the form to use inside Dockerfiles, Ansible playbooks, shell provisioning scripts, etc.

### Creating additional users

The same command works for any number of users — run it again with different details. Every user gets their own isolated workspace: sites, goals, funnels, and analytics data are scoped to the user that created them.

There is no role system in the self-hosted edition. Every account that exists can log in and manage its own sites. Teams, role-based access, and shared billing live in the cloud edition only.

### Resetting a password

If you forget the password for a user, the password reset flow at `/forgot-password` works normally (provided your `.env` has a working mail driver configured).

For a reset without email, use `tinker`:

```bash
php artisan tinker
>>> $u = \App\Models\User::where('email', 'jane@example.com')->first();
>>> $u->password = \Hash::make('new-password');
>>> $u->save();
```

### Validation rules

The command enforces:

| Field    | Rule                              |
|----------|-----------------------------------|
| name     | Required, string, max 255 chars   |
| email    | Required, valid email, unique     |
| password | Required, minimum 8 characters    |

If validation fails (e.g. the email already exists), the command exits with a non-zero status and prints the error — handy for scripting.

---

## After Creating the Admin

1. Visit `/login` and sign in with the credentials you just set.
2. Add your first website from the **Websites** page (`/account/sites`).
3. Copy the tracking snippet from the website's detail page and paste it into the `<head>` of your site.
4. Within a minute or two, live visitors will appear under **Live Stats**.

---

## FAQ

**Why isn't there a `/register` page?**
Self-hosted Statalog is designed for one person or one trusted team. Leaving registration open on a public-facing installation would let anyone create an account. Disabling it by default is the safer posture. If you want open registration, you can re-enable the route in `routes/auth.php` — but be aware of the implications.

**Can I automate admin creation during `docker build` / first boot?**
Yes — use the non-interactive form. A common pattern is an entrypoint script that runs `statalog:create-admin` only if `User::count() === 0`:

```bash
if [ "$(php artisan tinker --execute='echo \App\Models\User::count();')" = "0" ]; then
    php artisan statalog:create-admin \
        --name="$ADMIN_NAME" \
        --email="$ADMIN_EMAIL" \
        --password="$ADMIN_PASSWORD"
fi
```

**Does the command need a database?**
Yes — make sure you've run `php artisan migrate` before creating the first admin.

---

## Installing the Tracking Code

Once you've added a website from the **Websites** page, Statalog generates a unique `site_id` (e.g. `SA-A1B2C3D4E5F6G`) and a tracking snippet tied to it.

### Where to find the snippet

1. Log in at `/login`.
2. Open **Websites** → click your site → the **Tracking Script** card shows the snippet with a **Copy to clipboard** button.

The snippet looks like this:

```html
<script defer data-site-id="SA-A1B2C3D4E5F6G" src="https://analytics.example.com/js/tracker.js"></script>
```

Replace `analytics.example.com` with the domain where your Statalog instance is hosted. The `data-site-id` is unique per website.

### How to install it

Paste the snippet inside the `<head>` of every page you want to track, **before the closing `</head>` tag**:

```html
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>My Website</title>
    <!-- Statalog tracking -->
    <script defer data-site-id="SA-A1B2C3D4E5F6G" src="https://analytics.example.com/js/tracker.js"></script>
</head>
<body>
    ...
</body>
</html>
```

The `defer` attribute ensures the script never blocks page rendering.

### Framework-specific placement

| Stack                | Where to paste                                           |
|----------------------|----------------------------------------------------------|
| WordPress            | Appearance → Theme File Editor → `header.php` (before `</head>`). Or use a plugin like "Insert Headers and Footers". |
| Laravel Blade        | `resources/views/layouts/app.blade.php` inside the `<head>` block. |
| Next.js (App Router) | `app/layout.tsx` — add `<Script src="..." strategy="afterInteractive" data-site-id="..." />`. |
| Next.js (Pages)      | `pages/_document.tsx` inside `<Head>`. |
| Nuxt 3               | `nuxt.config.ts` → `app.head.script` array. |
| Astro                | `src/layouts/Layout.astro` inside the `<head>`. |
| SvelteKit            | `src/app.html` inside `<head>`. |
| Static HTML          | Paste into every `.html` file's `<head>`, or use a build step / server-side include. |
| Google Tag Manager   | New **Custom HTML** tag → paste the snippet → trigger: All Pages. |
| Shopify              | Online Store → Themes → Edit code → `layout/theme.liquid`, before `</head>`. |
| Webflow              | Project Settings → Custom Code → Head Code. |
| Ghost                | Settings → Code Injection → Site Header. |

### Verifying it works

After installing:

1. Visit any page on your website in a browser.
2. Open the **Live Stats** page in Statalog (`/account/live`) — you should see yourself appear within a few seconds.
3. If you don't, open your browser's DevTools → **Network** tab, filter by `collect`, and reload the page. You should see a request to `https://analytics.example.com/api/collect` returning a 200 status and a 1×1 transparent GIF.

### What gets tracked

Out of the box, the tracker sends the following on every page load:

- **Page URL** (path + query string; hash is stripped)
- **Referrer**
- **User agent** (server-side parsed into device / browser / OS)
- **Screen resolution** (`screen_width`, `screen_height`)
- **Language** (`navigator.language`)
- **UTM parameters** (`utm_source`, `utm_medium`, `utm_campaign`, `utm_content`, `utm_term`)
- **Page load time**
- **Session duration** (calculated when the user leaves the page)
- An **anonymous daily visitor ID** — an HMAC-SHA256 hash of IP + user agent + rotating daily salt. Never stored in reverse-lookable form, and rotates every 24 hours so visitors cannot be tracked across days.

**Never sent:** the visitor's raw IP address, cookies, or any personally identifiable information.

### Respecting Do Not Track

The tracker honors the browser's `DNT: 1` header. Visitors with DNT enabled are recorded as hits but without device/location fingerprinting.

### Single Page Applications (SPA)

The default tracker fires once on page load. If your site is a SPA and uses client-side routing, you need to notify Statalog on every route change. Call:

```js
window.statalog && window.statalog.trackPageview();
```

After your router navigates. Example hooks:

- **React Router:** inside a `useEffect` listening to `location.pathname`.
- **Vue Router:** a global `router.afterEach()` hook.
- **Next.js App Router:** a client component that listens to `usePathname()`.

### Custom events

To track conversions, button clicks, form submissions, or other custom actions:

```js
window.statalog.trackEvent('signup', { plan: 'pro', source: 'hero-cta' });
```

The first argument is the event name (free-form). The second is an optional properties object (keys and values stored as strings). Events show up under **Custom Events** in the dashboard.

### Tracking across subdomains

By default, each website is pinned to its exact domain. To include subdomains (e.g. `blog.example.com`, `app.example.com` all reporting under one site), edit the website and enable **Track subdomains**.

### Excluding your own visits

You have two options:

1. **Exclude by IP:** go to **Settings** → add your IP to **Excluded IP Addresses**. Hits from that IP will be dropped server-side.
2. **Client-side opt-out:** in a browser's DevTools console, run `localStorage.setItem('statalog-ignore', '1')`. The tracker skips reporting while that flag is set. Remove it with `localStorage.removeItem('statalog-ignore')`.

### Troubleshooting

| Symptom                                              | Likely cause                                                                             |
|------------------------------------------------------|------------------------------------------------------------------------------------------|
| Script loads (200) but **Live Stats** stays empty    | `data-site-id` doesn't match any site, or the page's domain differs from the site's domain + **Track subdomains** is off. |
| `collect` request returns CORS error                 | Your instance is on a different origin than the tracked site. CORS is allowed for all origins by default — check a proxy or firewall isn't stripping the `Access-Control-Allow-Origin` header. |
| Script returns 404                                   | The tracker file is missing from `public/js/tracker.js`. Re-run `composer install` or check file permissions. |
| Ad blockers blocking the script                      | Some uBlock filter lists match `/js/tracker.js`. Rename the file and adjust the snippet URL, or serve it via a reverse proxy on your tracked domain. |
| Visits from your own team inflate the numbers        | Add their IPs under **Settings → Excluded IPs**, or use the `localStorage` opt-out above. |

