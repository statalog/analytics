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

        $locale = $this->resolve($request, $supported, $default);

        app()->setLocale($locale);

        return $next($request);
    }

    /**
     * Resolution priority:
     * 1. Subdomain prefix (es.statalog.com → es; pt-br.statalog.com → pt_BR)
     *    — highest priority for marketing pages so each locale has its own
     *    canonical URL. Hyphens in subdomains map back to underscores in
     *    locale codes (DNS-safe vs Laravel-style).
     * 2. ?lang=xx query (one-shot, persisted via cookie)
     * 3. Authed user's saved locale
     * 4. statalog_locale cookie
     * 5. Accept-Language header
     * 6. Configured default
     */
    protected function resolve(Request $request, array $supported, string $default): string
    {
        $sub = $this->subdomainLocale($request);
        if ($sub && ($match = $this->matchLocale($sub, $supported))) return $match;

        $query = $request->query('lang');
        if ($query && ($match = $this->matchLocale($query, $supported))) return $match;

        $user = $request->user();
        if ($user && $user->locale && ($match = $this->matchLocale($user->locale, $supported))) {
            return $match;
        }

        $cookie = $request->cookie(self::COOKIE);
        if ($cookie && ($match = $this->matchLocale($cookie, $supported))) return $match;

        $accept = (string) $request->header('Accept-Language', '');
        foreach (array_filter(array_map('trim', explode(',', $accept))) as $part) {
            $raw = trim(explode(';', $part)[0]);
            if ($raw === '') continue;
            if ($match = $this->matchLocale($raw, $supported)) return $match;
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
