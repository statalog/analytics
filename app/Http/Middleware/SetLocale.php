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
     * 1. ?lang=xx query (one-shot, persisted via cookie)
     * 2. Authed user's saved locale
     * 3. statalog_locale cookie
     * 4. Accept-Language header
     * 5. Configured default
     */
    protected function resolve(Request $request, array $supported, string $default): string
    {
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
