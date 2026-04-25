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
    | Version
    |--------------------------------------------------------------------------
    */

    'version' => '1.0.0',

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
