<?php

return [

    // Pages
    'page_index'    => 'Importa da Google Analytics',
    'page_select'   => 'Scegli una proprietà GA',
    'page_progress' => 'Importazione in corso',
    'page_summary'  => 'Dati storici — :site',

    // Index
    'intro' => 'Importa visualizzazioni di pagina storiche, visitatori, pagine principali e fonti principali dalla Tua proprietà GA4 in Statalog. Utile quando ci si allontana da Google Analytics — non perdere i Tuoi numeri precedenti.',

    'oauth_not_configured_title' => 'Google OAuth non configurato',
    'oauth_not_configured_body'  => 'Per abilitare l\'importazione GA, registra un\'app Web OAuth 2.0 su console.cloud.google.com, abilita Google Analytics Data API e imposta l\'URI di reindirizzamento su :redirect. Quindi aggiungi GOOGLE_CLIENT_ID, GOOGLE_CLIENT_SECRET e GOOGLE_REDIRECT_URI al Tuo file .env e aggiorna.',

    'step1_title'      => 'Passaggio 1 — Accedi con Google',
    'step1_body'       => 'Richiederemo accesso in sola lettura al Tuo Google Analytics. Non vediamo mai i dati del Tuo account Google e puoi disconnetterti in qualsiasi momento.',
    'btn_connect'      => 'Connetti con Google',
    'connected_title'  => 'Connesso a Google Analytics',
    'connected_body'   => 'Scegli una proprietà GA4 da importare in uno dei Tuoi siti Statalog.',
    'btn_continue'     => 'Continua',
    'btn_disconnect'   => 'Disconnetti',

    'whats_imported_title' => 'Cosa viene importato',
    'whats_imported_1'     => 'Visitatori giornalieri, visualizzazioni di pagina, sessioni',
    'whats_imported_2'     => 'Frequenza di rimbalzo e durata media',
    'whats_imported_3'     => 'Top 50 pagine',
    'whats_imported_4'     => 'Top 20 fonti e paesi',
    'whats_imported_5'     => 'Dati GA4, fino a 14 mesi',

    'recent_imports'    => 'Importazioni recenti',
    'col_site'          => 'Sito',
    'col_ga_property'   => 'Proprietà GA',
    'col_range'         => 'Periodo',
    'col_status'        => 'Stato',
    'col_progress'      => 'Avanzamento',
    'days_progress'     => ':processed/:total giorni',
    'btn_view'          => 'Visualizza',
    'btn_progress'      => 'Avanzamento',

    // Select
    'no_properties_title' => 'Nessuna proprietà GA4 trovata',
    'no_properties_body'  => 'Assicurati che l\'account Google che hai collegato abbia accesso ad almeno una proprietà GA4. Le proprietà Universal Analytics non sono più supportate.',
    'label_property'      => 'Proprietà Google Analytics',
    'choose_property'     => '— Scegli una proprietà —',
    'label_target_site'   => 'Importa nel sito Statalog',
    'choose_site'         => '— Scegli un sito —',
    'hint_target_site'    => 'I dati storici importati verranno allegati a questo sito Statalog.',
    'label_history'       => 'Quanto storico',
    'history_1'           => 'Ultimo 1 mese',
    'history_3'           => 'Ultimi 3 mesi',
    'history_6'           => 'Ultimi 6 mesi',
    'history_12'          => 'Ultimi 12 mesi',
    'history_14'          => 'Ultimi 14 mesi (max GA4)',
    'btn_start_import'    => 'Avvia importazione',
    'btn_cancel'          => 'Annulla',

    // Progress
    'importing'                 => 'Importazione da Google Analytics',
    'days_processed'            => ':processed / :total giorni elaborati',
    'btn_view_imported'         => 'Visualizza dati importati',
    'btn_back_to_imports'       => 'Torna alle importazioni',
    'status_completed'          => 'Completato',
    'status_failed'             => 'Non riuscito',
    'importing_percent'         => 'Importazione — :percent%',

    // Summary
    'historical_subtitle' => 'Dati storici importati da Google Analytics',
    'stat_visitors'       => 'Visitatori',
    'stat_pageviews'      => 'Visualizzazioni di Pagina',
    'stat_sessions'       => 'Sessioni',
    'stat_avg_bounce'     => 'Frequenza di rimbalzo media',
    'pageviews_per_day'   => 'Visualizzazioni di pagina al giorno',
    'top_pages'           => 'Pagine principali',
    'top_sources'         => 'Fonti principali',
    'top_countries'       => 'Paesi principali',
    'col_page'            => 'Pagina',
    'col_pageviews'       => 'Visualizzazioni di Pagina',
    'col_source'          => 'Fonte',
    'col_visitors'        => 'Visitatori',
    'col_country'         => 'Paese',
    'direct'              => '(diretto)',
    'unknown'             => 'Sconosciuto',
    'no_data'             => 'Nessun dato',

];
