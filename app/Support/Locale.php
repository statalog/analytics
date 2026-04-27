<?php

namespace App\Support;

use Illuminate\Support\Facades\Request;

/**
 * Locale helpers for building subdomain-routed marketing URLs and hreflang tags.
 *
 * Locale code  →  Subdomain
 * ------------------------
 * en           → (bare app host — no subdomain)
 * es           → es.<host>
 * de           → de.<host>
 * pt_BR        → pt-br.<host>
 * zh_CN        → zh-cn.<host>
 */
class Locale
{
    /**
     * Build the canonical URL for a given locale code and path. Path defaults
     * to the current request URI so the language switcher stays on the page.
     */
    public static function url(string $code, ?string $path = null): string
    {
        $path = $path ?? '/' . ltrim(Request::path(), '/');
        $path = '/' . ltrim($path, '/');

        $base = (string) config('app.url');
        $appHost = (string) parse_url($base, PHP_URL_HOST);
        $scheme  = (string) (parse_url($base, PHP_URL_SCHEME) ?: 'https');

        if ($code === self::defaultCode() || !$appHost) {
            return $scheme . '://' . $appHost . $path;
        }

        return $scheme . '://' . self::subdomainFor($code) . '.' . $appHost . $path;
    }

    /** Convert a locale code to its DNS-safe subdomain form (pt_BR → pt-br). */
    public static function subdomainFor(string $code): string
    {
        return strtolower(str_replace('_', '-', $code));
    }

    /** Default locale (bare-domain locale). */
    public static function defaultCode(): string
    {
        return (string) config('statalog.locale_default', 'en');
    }

    /** Returns true when the current request is on a locale subdomain. */
    public static function isOnSubdomain(): bool
    {
        $host = strtolower((string) Request::getHost());
        $appHost = strtolower((string) parse_url((string) config('app.url'), PHP_URL_HOST));
        return $host !== $appHost;
    }
}
