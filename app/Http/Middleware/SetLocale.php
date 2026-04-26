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
        if ($query && in_array($query, $supported, true)) {
            Cookie::queue(self::COOKIE, $query, 60 * 24 * 365);
            return $query;
        }

        $user = $request->user();
        if ($user && $user->locale && in_array($user->locale, $supported, true)) {
            return $user->locale;
        }

        $cookie = $request->cookie(self::COOKIE);
        if ($cookie && in_array($cookie, $supported, true)) {
            return $cookie;
        }

        $accept = (string) $request->header('Accept-Language', '');
        foreach (array_filter(array_map('trim', explode(',', $accept))) as $part) {
            $code = strtolower(substr(explode(';', $part)[0], 0, 2));
            if ($code && in_array($code, $supported, true)) {
                return $code;
            }
        }

        return $default;
    }
}
