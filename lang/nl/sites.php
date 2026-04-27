<?php

return [

    // Pagina's
    'page_index'  => 'Websites',
    'page_create' => 'Website toevoegen',
    'page_show'   => 'Websitedetails',
    'page_edit'   => 'Website bewerken',

    // Velden
    'field_name'             => 'Sitenaam',
    'field_domain'           => 'Domein',
    'field_timezone'         => 'Tijdzone',
    'field_track_subdomains' => 'Subdomeinen traceren',
    'field_is_active'        => 'Actief',
    'field_site_id'          => 'Site-ID',

    // Tips
    'hint_domain'           => 'Het domein van uw website, zonder http:// (bijv. voorbeeld.com).',
    'hint_track_subdomains' => 'Indien ingeschakeld, worden hits van elk subdomein vastgelegd.',
    'hint_timezone'         => 'Wordt gebruikt om statistieken per dag in uw lokale tijdzone in te delen.',

    // Tracking
    'tracking_snippet_title' => 'Tracking-script',
    'tracking_snippet_hint'  => 'Kopieer dit fragment naar de <head> van uw website.',
    'tracking_copy'          => 'Kopiëren naar klembord',
    'tracking_copied'        => 'Gekopieerd!',
    'tracking_copy_code'     => 'Code kopiëren',

    // Bottracking
    'bot_track_label'    => 'Botverkeer opslaan',
    'bot_track_recommended' => 'Aanbevolen',
    'bot_track_hint'     => 'Sterk aanbevolen — track crawlers (Googlebot, Bingbot, AI-schrapers zoals GPTBot en ClaudeBot, SEO-tools, enz.) om te begrijpen wie uw site indexeert en exploiteert. Bothits zijn altijd standaard uitgesloten van uw normale statistieken, zodat ze uw menselijke analyses nooit vervuilen. U krijgt een speciale Bots-pagina met een uitsplitsing per bot, categorie en pagina, en u kunt bots in of uit schakelen op elk rapport.',
    'bot_detection_label' => 'Botdetectie wordt automatisch uitgevoerd',
    'bot_detection_hint'  => 'Statalog identificeert zoekmachinecrawlers (Googlebot, Bingbot), AI-schrapers (GPTBot, ClaudeBot, PerplexityBot) en SEO-tools met behulp van geverifieerde User-Agent- en IP-handtekeningen. Bothits tellen nooit mee voor uw factureerbare paginaweergaven en worden uitgesloten van menselijke statistieken — ze verschijnen op de speciale Bots-pagina.',
    'bot_snippet_title'  => 'Bottracking-fragment',
    'bot_snippet_intro'  => 'U hebt Opslaan van botverkeer ingeschakeld. Vervang uw huidige trackingfragment door dit. Het voegt een <code>&lt;noscript&gt;</code>-pixel toe die crawlers (Googlebot, GPTBot, enz.) automatisch ophalen — de pagina-URL wordt gedetecteerd op basis van het verzoek zonder extra configuratie nodig.',
    'bot_snippet_note'   => 'Plak dit eenmaal in de globale lay-out van uw site. Geen plaatsen als waarden om te vervangen.',

    // Openbaar dashboard
    'public_title'           => 'Openbaar dashboard',
    'public_enable'          => 'Dit dashboard openbaar toegankelijk maken',
    'public_password_label'  => 'Met wachtwoord beveiligen (optioneel)',
    'public_password_placeholder' => 'Laat leeg om het huidige wachtwoord te behouden',
    'public_url'             => 'Deelbare link',
    'public_sections'        => 'Secties om weer te geven',

    // Sectielabels
    'section_chart'       => 'Grafiek',
    'section_pages'       => 'Topbladzijden',
    'section_sources'     => 'Verkeersbronnen',
    'section_locations'   => 'Locaties',
    'section_devices'     => 'Apparaten',
    'section_browsers'    => 'Browsers',
    'section_os'          => 'Besturingssystemen',
    'section_resolutions' => 'Schermresoluties',

    // Statistieken op index
    'stats_today'      => 'Vandaag',
    'stats_this_month' => 'Deze maand',
    'stats_last_month' => 'Vorige maand',
    'stats_hits'       => 'hits',

    // Statistiekenkaarten op index
    'card_total_visitors'  => 'Totale bezoekers',
    'card_total_sessions'  => 'Totale sessies',
    'card_total_pageviews' => 'Totale paginaweergaven',
    'card_sites_tracked'   => 'Getrackte sites',
    'vs_previous'          => 'vs vorig',
    'your_websites'        => 'Uw websites',
    'visitors'             => 'Bezoekers',
    'sessions'             => 'Sessies',
    'pageviews'            => 'Paginaweergaven',
    'tracking_paused'      => 'Tracking onderbroken',
    'site_settings'        => 'Site-instellingen',
    'open_dashboard'       => 'Dashboard van :site openen',

    // Plangebruik
    'plan_usage'         => 'Plangebruik',
    'billing_period'     => 'Factureringsperiode: :from – :to',
    'day_of_total'       => 'Dag :elapsed van :total',
    'pageviews_label'    => 'paginaweergaven',
    'percent_used'       => ':percent% gebruikt',
    'unlimited'          => 'Onbeperkt',
    'resets'             => 'Wordt opnieuw ingesteld :date',

    // Toonpagina
    'website_details'        => 'Websitedetails',
    'delete_website_title'   => 'Website verwijderen?',
    'delete_website_warn'    => 'U staat op het punt de website :name en alle analysegegevens (paginaweergaven, events, fouten, heatmaps) permanent te verwijderen. Dit kan niet ongedaan worden gemaakt.',
    'confirm_password_label' => 'Bevestig uw wachtwoord',
    'confirm_password_placeholder' => 'Uw accountwachtwoord',

    // Knoppen
    'btn_add_site'    => 'Website toevoegen',
    'btn_save'        => 'Website opslaan',
    'btn_delete'      => 'Website verwijderen',
    'btn_view_stats'  => 'Statistieken weergeven',

    // Bevestig
    'confirm_delete' => 'Weet u zeker dat u deze website wilt verwijderen? Alle analysegegevens worden in ClickHouse bewaard.',

    // Lege toestanden
    'no_sites'        => 'Nog geen websites toegevoegd.',
    'no_sites_cta'    => 'Voeg uw eerste website toe om tracking te starten.',

    // Succesberichten
    'msg_added'   => 'Website toegevoegd. Kopieer het trackingscript hieronder.',
    'msg_updated' => 'Website bijgewerkt.',
    'msg_removed' => 'Website verwijderd.',

    // Tijdelijke aanduidingen formulier maken
    'placeholder_name'   => 'Mijn website',
    'placeholder_domain' => 'voorbeeld.com',

    // Accountkiezer
    'account_picker_title'   => 'Kies een account',
    'account_picker_logged'  => 'Aangemeld als :email',
    'account_picker_choose_title' => 'Account kiezen',
    'account_your'           => 'Uw account',
    'account_no_sites_yet'   => 'Nog geen sites',
    'account_site_one'       => ':count site',
    'account_site_many'      => ':count sites',
    'btn_logout'             => 'Afmelden',

];
