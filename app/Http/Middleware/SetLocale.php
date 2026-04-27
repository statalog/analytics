<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class SetLocale
{
    public const COOKIE = 'statalog_locale';

    public function handle(Request $request, Closure $next)
    {
        $supported = array_keys(config('statalog.locales', ['en' => 'English']));
        $default   = config('statalog.locale_default', 'en');

        // Dashboard/admin routes always live on the bare app domain. If we
        // hit one on a locale subdomain (es.statalog.com/account/dashboard)
        // redirect to the bare domain so user.locale drives the language.
        if ($this->isDashboardPath($request) && $this->subdomainLocale($request)) {
            $appHost = (string) parse_url((string) config('app.url'), PHP_URL_HOST);
            $scheme  = $request->getScheme();
            $target  = $scheme . '://' . $appHost . '/' . ltrim($request->path(), '/');
            if ($qs = $request->getQueryString()) $target .= '?' . $qs;
            return redirect($target, 302);
        }

        $locale = $this->resolve($request, $supported, $default);

        app()->setLocale($locale);

        return $next($request);
    }

    protected function isDashboardPath(Request $request): bool
    {
        return $request->is('account/*') || $request->is('admin/*');
    }

    /**
     * Resolution priority:
     *   - Marketing pages (public landing/docs/blog/legal/etc.):
     *     1. Subdomain prefix (es.statalog.com → es)
     *     2. ?lang=xx query — explicit one-shot override
     *     3. Configured default (bare domain = default, period)
     *
     *   - Dashboard / admin pages (/account/*, /admin/*):
     *     1. Subdomain prefix
     *     2. ?lang=xx query
     *     3. Authed user's saved locale
     *     4. Configured default
     *
     * Marketing pages deliberately do NOT consult user.locale — the URL is
     * the source of truth. Otherwise an authed user with locale=ro who clicks
     * "English" in the marketing switcher (which links to statalog.com)
     * would still see Romanian, because user.locale would override the URL.
     */
    protected function resolve(Request $request, array $supported, string $default): string
    {
        $sub = $this->subdomainLocale($request);
        if ($sub && ($match = $this->matchLocale($sub, $supported))) return $match;

        $query = $request->query('lang');
        if ($query && ($match = $this->matchLocale($query, $supported))) return $match;

        // Only dashboard/admin paths fall through to the authed user's locale.
        if ($request->is('account/*') || $request->is('admin/*')) {
            $user = $request->user();
            if ($user && $user->locale && ($match = $this->matchLocale($user->locale, $supported))) {
                return $match;
            }
        }

        return $default;
    }

    /**
     * Returns the subdomain prefix when the request hostname is a locale
     * subdomain of the configured app URL. E.g.
     *   es.statalog.com         → 'es'
     *   pt-br.statalog.com      → 'pt-br'  (matchLocale will normalize to pt_BR)
     *   statalog.com            → null     (bare domain, no subdomain)
     *   panel.statalog.com      → null     (not a locale prefix shape)
     *
     * Detection is conservative: we only treat it as a locale subdomain when
     * the prefix is one of the configured supported codes. That way unrelated
     * subdomains (panel, app, mail, …) pass through untouched.
     */
    protected function subdomainLocale(Request $request): ?string
    {
        $host = strtolower((string) $request->getHost());
        $appHost = strtolower((string) parse_url((string) config('app.url'), PHP_URL_HOST));
        if (!$host || !$appHost || $host === $appHost) return null;

        // Only consider hosts that are direct subdomains of the configured app host.
        if (!str_ends_with($host, '.' . $appHost)) return null;

        $prefix = substr($host, 0, -strlen('.' . $appHost));
        if ($prefix === '' || str_contains($prefix, '.')) return null; // multi-level subdomains skipped

        return $prefix;
    }

    /**
     * Match an incoming locale against the supported list. Tries the full code
     * first (e.g. pt_BR), then the language prefix (e.g. pt). Comparison is
     * case-insensitive and accepts both '-' and '_' separators on input.
     */
    protected function matchLocale(string $input, array $supported): ?string
    {
        $normalized = str_replace('-', '_', strtolower($input));

        // Exact (case-insensitive) match — supports pt_BR / zh_CN style codes.
        foreach ($supported as $code) {
            if (strcasecmp($code, $normalized) === 0) return $code;
        }

        // Prefix fallback — pt-BR → pt if 'pt' is supported but 'pt_BR' isn't.
        $prefix = strstr($normalized, '_', true) ?: $normalized;
        foreach ($supported as $code) {
            if (strcasecmp($code, $prefix) === 0) return $code;
        }

        return null;
    }
}
