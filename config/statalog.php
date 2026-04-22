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

    'geoip_database' => env('STATALOG_GEOIP_DATABASE', storage_path('app/geoip/GeoLite2-City.mmdb')),

];
