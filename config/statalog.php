<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Statalog Edition
    |--------------------------------------------------------------------------
    |
    | Either "community" (open source, self-hosted) or "cloud" (SaaS).
    | The cloud package is only loaded when this is set to "cloud" and the
    | packages/cloud path composer repository is installed.
    |
    */

    'edition' => env('STATALOG_EDITION', 'community'),

    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    */

    'name' => env('STATALOG_NAME', 'Statalog'),

    /*
    |--------------------------------------------------------------------------
    | Marketing URLs
    |--------------------------------------------------------------------------
    */

    'url' => env('STATALOG_URL', 'https://statalog.com'),

    'demo_url'      => env('STATALOG_DEMO_URL', ''),
    'demo_email'    => env('STATALOG_DEMO_EMAIL', ''),
    'demo_password' => env('STATALOG_DEMO_PASSWORD', ''),

    /*
    |--------------------------------------------------------------------------
    | Accent Color Scheme
    |--------------------------------------------------------------------------
    |
    | Controls the primary accent color used throughout the admin UI.
    | Self-hosted admins can rebrand their instance by setting this in .env.
    |
    | Available: blue (default), indigo, violet, emerald, cyan, teal, amber, rose, ember
    |
    */

    'accent' => env('STATALOG_ACCENT', 'emerald'),

    /*
    |--------------------------------------------------------------------------
    | Supported Locales
    |--------------------------------------------------------------------------
    |
    | Self-hosted installs choose which languages are active via the
    | STATALOG_LOCALES env var (comma-separated list of locale codes).
    | The map below is the catalog of officially packaged languages —
    | only codes present here AND in STATALOG_LOCALES are exposed in the UI.
    |
    | Each enabled locale must have a matching `lang/{code}/` directory
    | (and `packages/cloud/lang/{code}/` when the cloud package is installed).
    |
    | Add a new language? Drop the translation files in, add the code +
    | native name to `locale_catalog` below, and bump it in your .env.
    |
    */

    'locales' => (function () {
        $catalog = [
            'en' => 'English',
            'es' => 'Español',
            'de' => 'Deutsch',
            'fr' => 'Français',
            'it' => 'Italiano',
            'pt_BR' => 'Português (Brasil)',
            'pt_PT' => 'Português (Portugal)',
            'nl' => 'Nederlands',
            'pl' => 'Polski',
            'ro' => 'Română',
            'ru' => 'Русский',
            'tr' => 'Türkçe',
            'cs' => 'Čeština',
            'sv' => 'Svenska',
            'da' => 'Dansk',
            'nb' => 'Norsk',
            'fi' => 'Suomi',
            'el' => 'Ελληνικά',
            'hu' => 'Magyar',
            'uk' => 'Українська',
            'ja' => '日本語',
            'zh_CN' => '中文 (简体)',
            'zh_TW' => '中文 (繁體)',
            'ko' => '한국어',
            'ar' => 'العربية',
            'he' => 'עברית',
            'hi' => 'हिन्दी',
        ];

        $enabled = array_filter(array_map('trim', explode(',', env('STATALOG_LOCALES', 'en'))));

        return array_intersect_key($catalog, array_flip($enabled));
    })(),

    'locale_default' => env('STATALOG_LOCALE_DEFAULT', 'en'),

    /*
    |--------------------------------------------------------------------------
    | Visitor Anonymization
    |--------------------------------------------------------------------------
    |
    | HMAC-SHA256 key used to anonymise visitor fingerprints. Rotated daily
    | together with the request date so that a visitor cannot be tracked
    | across days.
    |
    */

    'visitor_salt' => env('STATALOG_VISITOR_SALT', ''),

    /*
    |--------------------------------------------------------------------------
    | GeoIP Database
    |--------------------------------------------------------------------------
    |
    | Path to the MaxMind GeoLite2 City database. Defaults to the
    | storage/app/geoip folder.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Google PageSpeed / Core Web Vitals API Key
    |--------------------------------------------------------------------------
    */

    'pagespeed_key' => env('GOOGLE_PAGESPEED_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | REST API Key
    |--------------------------------------------------------------------------
    |
    | A secret key that protects the /api/v1/* endpoints.
    | Leave empty to disable API access entirely.
    | Cloud installations override this with per-user DB-backed keys.
    |
    */

    'api_key' => env('STATALOG_API_KEY', ''),

    'geoip_database' => (function () {
        $val = env('STATALOG_GEOIP_DATABASE', '');
        if ($val === '') {
            return storage_path('app/geoip/GeoLite2-City.mmdb');
        }
        return str_starts_with($val, '/') || (strlen($val) > 1 && $val[1] === ':')
            ? $val
            : base_path($val);
    })(),

];
