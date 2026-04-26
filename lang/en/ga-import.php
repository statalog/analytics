<?php

return [

    // Pages
    'page_index'    => 'Import from Google Analytics',
    'page_select'   => 'Choose a GA property',
    'page_progress' => 'Import in progress',
    'page_summary'  => 'Historical data — :site',

    // Index
    'intro' => 'Pull historical pageviews, visitors, top pages and top sources from your GA4 property into Statalog. Useful when switching away from Google Analytics — don\'t lose your past numbers.',

    'oauth_not_configured_title' => 'Google OAuth not configured',
    'oauth_not_configured_body'  => 'To enable GA import, register an OAuth 2.0 Web app at console.cloud.google.com, enable the Google Analytics Data API, and set the redirect URI to :redirect. Then add GOOGLE_CLIENT_ID, GOOGLE_CLIENT_SECRET and GOOGLE_REDIRECT_URI to your .env file and refresh.',

    'step1_title'      => 'Step 1 — Sign in with Google',
    'step1_body'       => 'We\'ll request read-only access to your Google Analytics. We never see your Google account data and you can disconnect any time.',
    'btn_connect'      => 'Connect with Google',
    'connected_title'  => 'Connected to Google Analytics',
    'connected_body'   => 'Pick a GA4 property to import into one of your Statalog sites.',
    'btn_continue'     => 'Continue',
    'btn_disconnect'   => 'Disconnect',

    'whats_imported_title' => 'What gets imported',
    'whats_imported_1'     => 'Daily visitors, pageviews, sessions',
    'whats_imported_2'     => 'Bounce rate & avg duration',
    'whats_imported_3'     => 'Top 50 pages',
    'whats_imported_4'     => 'Top 20 sources & countries',
    'whats_imported_5'     => 'GA4 data, up to 14 months',

    'recent_imports'    => 'Recent imports',
    'col_site'          => 'Site',
    'col_ga_property'   => 'GA property',
    'col_range'         => 'Range',
    'col_status'        => 'Status',
    'col_progress'      => 'Progress',
    'days_progress'     => ':processed/:total days',
    'btn_view'          => 'View',
    'btn_progress'      => 'Progress',

    // Select
    'no_properties_title' => 'No GA4 properties found',
    'no_properties_body'  => 'Make sure the Google account you connected has access to at least one GA4 property. Universal Analytics properties are no longer supported.',
    'label_property'      => 'Google Analytics property',
    'choose_property'     => '— Choose a property —',
    'label_target_site'   => 'Import into Statalog site',
    'choose_site'         => '— Choose a site —',
    'hint_target_site'    => 'The imported historical data will be attached to this Statalog site.',
    'label_history'       => 'How much history',
    'history_1'           => 'Last 1 month',
    'history_3'           => 'Last 3 months',
    'history_6'           => 'Last 6 months',
    'history_12'          => 'Last 12 months',
    'history_14'          => 'Last 14 months (GA4 max)',
    'btn_start_import'    => 'Start import',
    'btn_cancel'          => 'Cancel',

    // Progress
    'importing'                 => 'Importing from Google Analytics',
    'days_processed'            => ':processed / :total days processed',
    'btn_view_imported'         => 'View imported data',
    'btn_back_to_imports'       => 'Back to imports',
    'status_completed'          => 'Completed',
    'status_failed'             => 'Failed',
    'importing_percent'         => 'Importing — :percent%',

    // Summary
    'historical_subtitle' => 'Historical data imported from Google Analytics',
    'stat_visitors'       => 'Visitors',
    'stat_pageviews'      => 'Pageviews',
    'stat_sessions'       => 'Sessions',
    'stat_avg_bounce'     => 'Avg bounce rate',
    'pageviews_per_day'   => 'Pageviews per day',
    'top_pages'           => 'Top pages',
    'top_sources'         => 'Top sources',
    'top_countries'       => 'Top countries',
    'col_page'            => 'Page',
    'col_pageviews'       => 'Pageviews',
    'col_source'          => 'Source',
    'col_visitors'        => 'Visitors',
    'col_country'         => 'Country',
    'direct'              => '(direct)',
    'unknown'             => 'Unknown',
    'no_data'             => 'No data',

];
