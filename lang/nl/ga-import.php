<?php

return [

    // Pagina's
    'page_index'    => 'Importeren vanuit Google Analytics',
    'page_select'   => 'Kies een GA-eigenschap',
    'page_progress' => 'Import in uitvoering',
    'page_summary'  => 'Historische gegevens — :site',

    // Index
    'intro' => 'Trek historische paginaweergaven, bezoekers, topbladzijden en toppbronnen vanuit uw GA4-eigenschap naar Statalog. Nuttig bij het overschakelen van Google Analytics — verlies uw eerdere getallen niet.',

    'oauth_not_configured_title' => 'Google OAuth niet geconfigureerd',
    'oauth_not_configured_body'  => 'Als u GA-import wilt inschakelen, registreert u een OAuth 2.0-web-app op console.cloud.google.com, schakelt u de Google Analytics Data API in en stelt u de omleidings-URI in op :redirect. Voeg vervolgens GOOGLE_CLIENT_ID, GOOGLE_CLIENT_SECRET en GOOGLE_REDIRECT_URI toe aan uw .env-bestand en vernieuw.',

    'step1_title'      => 'Stap 1 — Meld u aan bij Google',
    'step1_body'       => 'We vragen om alleen-lezen toegang tot uw Google Analytics. We zien uw Google-accountgegevens nooit en u kunt altijd verbreken.',
    'btn_connect'      => 'Verbinding maken met Google',
    'connected_title'  => 'Verbonden met Google Analytics',
    'connected_body'   => 'Kies een GA4-eigenschap om in één van uw Statalog-sites te importeren.',
    'btn_continue'     => 'Doorgaan',
    'btn_disconnect'   => 'Verbreken',

    'whats_imported_title' => 'Wat wordt geïmporteerd',
    'whats_imported_1'     => 'Dagelijkse bezoekers, paginaweergaven, sessies',
    'whats_imported_2'     => 'Stuiteringspercentage en avg-duur',
    'whats_imported_3'     => 'Top 50 pagina\'s',
    'whats_imported_4'     => 'Top 20 bronnen en landen',
    'whats_imported_5'     => 'GA4-gegevens, tot 14 maanden',

    'recent_imports'    => 'Recente imports',
    'col_site'          => 'Site',
    'col_ga_property'   => 'GA-eigenschap',
    'col_range'         => 'Bereik',
    'col_status'        => 'Status',
    'col_progress'      => 'Voortgang',
    'days_progress'     => ':processed/:total dagen',
    'btn_view'          => 'Bekijken',
    'btn_progress'      => 'Voortgang',

    // Selecteren
    'no_properties_title' => 'Geen GA4-eigenschappen gevonden',
    'no_properties_body'  => 'Zorg ervoor dat het Google-account waarmee u verbinding hebt gemaakt, toegang heeft tot ten minste één GA4-eigenschap. Universal Analytics-eigenschappen worden niet meer ondersteund.',
    'label_property'      => 'Google Analytics-eigenschap',
    'choose_property'     => '— Kies een eigenschap —',
    'label_target_site'   => 'Importeren naar Statalog-site',
    'choose_site'         => '— Kies een site —',
    'hint_target_site'    => 'De geïmporteerde historische gegevens worden aan deze Statalog-site gekoppeld.',
    'label_history'       => 'Hoeveel geschiedenis',
    'history_1'           => 'Afgelopen 1 maand',
    'history_3'           => 'Afgelopen 3 maanden',
    'history_6'           => 'Afgelopen 6 maanden',
    'history_12'          => 'Afgelopen 12 maanden',
    'history_14'          => 'Afgelopen 14 maanden (GA4-maximum)',
    'btn_start_import'    => 'Import starten',
    'btn_cancel'          => 'Annuleren',

    // Voortgang
    'importing'                 => 'Importeren vanuit Google Analytics',
    'days_processed'            => ':processed / :total dagen verwerkt',
    'btn_view_imported'         => 'Geïmporteerde gegevens bekijken',
    'btn_back_to_imports'       => 'Terug naar imports',
    'status_completed'          => 'Voltooid',
    'status_failed'             => 'Mislukt',
    'importing_percent'         => 'Importeren — :percent%',

    // Samenvatting
    'historical_subtitle' => 'Historische gegevens geïmporteerd van Google Analytics',
    'stat_visitors'       => 'Bezoekers',
    'stat_pageviews'      => 'Paginaweergaven',
    'stat_sessions'       => 'Sessies',
    'stat_avg_bounce'     => 'Avg stuiteringspercentage',
    'pageviews_per_day'   => 'Paginaweergaven per dag',
    'top_pages'           => 'Topbladzijden',
    'top_sources'         => 'Toppbronnen',
    'top_countries'       => 'Toplanden',
    'col_page'            => 'Pagina',
    'col_pageviews'       => 'Paginaweergaven',
    'col_source'          => 'Bron',
    'col_visitors'        => 'Bezoekers',
    'col_country'         => 'Land',
    'direct'              => '(direct)',
    'unknown'             => 'Onbekend',
    'no_data'             => 'Geen gegevens',

];
