<?php

return [

    // Pages
    'page_index'  => 'Siti',
    'page_create' => 'Aggiungi Sito',
    'page_show'   => 'Dettagli Sito',
    'page_edit'   => 'Modifica Sito',

    // Fields
    'field_name'             => 'Nome Sito',
    'field_domain'           => 'Dominio',
    'field_timezone'         => 'Fuso Orario',
    'field_track_subdomains' => 'Traccia sottodomini',
    'field_is_active'        => 'Attivo',
    'field_site_id'          => 'ID Sito',

    // Hints
    'hint_domain'           => 'Il Tuo dominio del sito, senza http:// (es: esempio.it).',
    'hint_track_subdomains' => 'Se abilitato, i clic da qualsiasi sottodominio verranno registrati.',
    'hint_timezone'         => 'Utilizzato per raggruppare le statistiche per giorno nel Tuo fuso orario locale.',

    // Tracking
    'tracking_snippet_title' => 'Script di Tracciamento',
    'tracking_snippet_hint'  => 'Copia questo frammento nel <head> del Tuo sito.',
    'tracking_copy'          => 'Copia negli appunti',
    'tracking_copied'        => 'Copiato!',
    'tracking_copy_code'     => 'Copia codice',

    // Bot tracking
    'bot_track_label'    => 'Archivia traffico bot',
    'bot_track_recommended' => 'Consigliato',
    'bot_track_hint'     => 'Altamente consigliato — traccia crawler (Googlebot, Bingbot, scraper IA come GPTBot e ClaudeBot, strumenti SEO, ecc.) per capire chi sta indicizzando e minando il Tuo sito. I clic dei bot sono sempre esclusi dalle Tue statistiche regolari per impostazione predefinita, quindi non inquineranno mai la Tua analisi umana. Ottieni una pagina dedicata ai Bot con una suddivisione per bot, categoria e pagina, e puoi attivare/disattivare i bot in qualsiasi report.',
    'bot_detection_label' => 'Rilevamento bot eseguito automaticamente',
    'bot_detection_hint'  => 'Statalog identifica i crawler dei motori di ricerca (Googlebot, Bingbot), i scraper di IA (GPTBot, ClaudeBot, PerplexityBot) e gli strumenti SEO utilizzando User-Agent verificato e le firme IP. I clic dei bot non contano mai verso le Tue visualizzazioni di pagina fatturabili e vengono esclusi dalle statistiche umane — compaiono sulla pagina dedicata ai Bot.',
    'bot_snippet_title'  => 'Frammento di tracciamento bot',
    'bot_snippet_intro'  => 'Hai Archivia traffico bot abilitato. Sostituisci il Tuo attuale frammento di tracciamento con questo. Aggiunge un pixel <code>&lt;noscript&gt;</code> che i crawler (Googlebot, GPTBot, ecc.) recuperano automaticamente — l\'URL della pagina viene rilevato dalla richiesta senza bisogno di configurazione aggiuntiva.',
    'bot_snippet_note'   => 'Incolla questo una volta nel layout globale del Tuo sito. Nessun segnaposto da sostituire.',

    // Public dashboard
    'public_title'           => 'Pannello Pubblico',
    'public_enable'          => 'Rendi questo pannello accessibile pubblicamente',
    'public_password_label'  => 'Proteggi con password (opzionale)',
    'public_password_placeholder' => 'Lascia vuoto per mantenere la password attuale',
    'public_url'             => 'Link condivisibile',
    'public_sections'        => 'Sezioni da mostrare',

    // Section labels
    'section_chart'       => 'Grafico',
    'section_pages'       => 'Pagine Principali',
    'section_sources'     => 'Fonti di Traffico',
    'section_locations'   => 'Posizioni',
    'section_devices'     => 'Dispositivi',
    'section_browsers'    => 'Browser',
    'section_os'          => 'Sistemi Operativi',
    'section_resolutions' => 'Risoluzioni dello Schermo',

    // Stats on index
    'stats_today'      => 'Oggi',
    'stats_this_month' => 'Questo mese',
    'stats_last_month' => 'Mese scorso',
    'stats_hits'       => 'clic',

    // Stats cards on index
    'card_total_visitors'  => 'Visitatori Totali',
    'card_total_sessions'  => 'Sessioni Totali',
    'card_total_pageviews' => 'Visualizzazioni di Pagina Totali',
    'card_sites_tracked'   => 'Siti Tracciati',
    'vs_previous'          => 'vs precedente',
    'your_websites'        => 'I Tuoi Siti',
    'visitors'             => 'Visitatori',
    'sessions'             => 'Sessioni',
    'pageviews'            => 'Visualizzazioni di Pagina',
    'tracking_paused'      => 'Tracciamento in pausa',
    'site_settings'        => 'Impostazioni sito',
    'open_dashboard'       => 'Apri pannello :site',

    // Plan usage
    'plan_usage'         => 'Utilizzo del piano',
    'billing_period'     => 'Periodo di fatturazione: :from – :to',
    'day_of_total'       => 'Giorno :elapsed di :total',
    'pageviews_label'    => 'visualizzazioni di pagina',
    'percent_used'       => ':percent% utilizzato',
    'unlimited'          => 'Illimitato',
    'resets'             => 'Resetta :date',

    // Show page
    'website_details'        => 'Dettagli Sito',
    'delete_website_title'   => 'Elimina sito?',
    'delete_website_warn'    => 'Stai per eliminare permanentemente :name e tutti i Tuoi dati analitici (visualizzazioni di pagina, eventi, errori, mappe di calore). Questo non può essere annullato.',
    'confirm_password_label' => 'Conferma la Tua password',
    'confirm_password_placeholder' => 'La Tua password dell\'account',

    // Buttons
    'btn_add_site'    => 'Aggiungi Sito',
    'btn_save'        => 'Salva Sito',
    'btn_delete'      => 'Elimina Sito',
    'btn_view_stats'  => 'Visualizza Statistiche',

    // Confirm
    'confirm_delete' => 'Sei sicuro di voler eliminare questo sito? Tutti i dati analitici verranno conservati in ClickHouse.',

    // Empty states
    'no_sites'        => 'Nessun sito aggiunto ancora.',
    'no_sites_cta'    => 'Aggiungi il Tuo primo sito per iniziare a tracciare.',

    // Success messages
    'msg_added'   => 'Sito aggiunto. Copia lo script di tracciamento qui sotto.',
    'msg_updated' => 'Sito aggiornato.',
    'msg_removed' => 'Sito rimosso.',

    // Create form placeholders
    'placeholder_name'   => 'Il Mio Sito',
    'placeholder_domain' => 'esempio.it',

    // Account picker
    'account_picker_title'   => 'Scegli un account',
    'account_picker_logged'  => 'Acceduto come :email',
    'account_picker_choose_title' => 'Scegli account',
    'account_your'           => 'Il Tuo account',
    'account_no_sites_yet'   => 'Nessun sito ancora',
    'account_site_one'       => ':count sito',
    'account_site_many'      => ':count siti',
    'btn_logout'             => 'Esci',

];
