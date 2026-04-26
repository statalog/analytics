<?php

return [

    // Pages
    'page_index'  => 'Websites',
    'page_create' => 'Add Website',
    'page_show'   => 'Website Details',
    'page_edit'   => 'Edit Website',

    // Fields
    'field_name'             => 'Site Name',
    'field_domain'           => 'Domain',
    'field_timezone'         => 'Timezone',
    'field_track_subdomains' => 'Track subdomains',
    'field_is_active'        => 'Active',
    'field_site_id'          => 'Site ID',

    // Hints
    'hint_domain'           => 'Your website domain, without http:// (e.g. example.com).',
    'hint_track_subdomains' => 'When enabled, hits from any subdomain will be recorded.',
    'hint_timezone'         => 'Used to group stats by day in your local timezone.',

    // Tracking
    'tracking_snippet_title' => 'Tracking Script',
    'tracking_snippet_hint'  => 'Copy this snippet into the <head> of your website.',
    'tracking_copy'          => 'Copy to clipboard',
    'tracking_copied'        => 'Copied!',
    'tracking_copy_code'     => 'Copy code',

    // Bot tracking
    'bot_track_label'    => 'Store bot traffic',
    'bot_track_recommended' => 'Recommended',
    'bot_track_hint'     => 'Highly recommended — track crawlers (Googlebot, Bingbot, AI scrapers like GPTBot and ClaudeBot, SEO tools, etc.) to understand who\'s indexing and mining your site. Bot hits are always excluded from your regular stats by default, so they never pollute your human analytics. You get a dedicated Bots page with a breakdown by bot, category, and page, and you can toggle bots in or out on any report.',
    'bot_snippet_title'  => 'Bot tracking snippet',
    'bot_snippet_intro'  => 'You have Store bot traffic enabled. Replace your current tracking snippet with this one. It adds a <noscript> pixel that crawlers (Googlebot, GPTBot, etc.) fetch automatically — the page URL is detected from the request with no extra configuration needed.',
    'bot_snippet_note'   => 'Paste this once in your site\'s global layout. No placeholders to replace.',

    // Public dashboard
    'public_title'           => 'Public Dashboard',
    'public_enable'          => 'Make this dashboard publicly accessible',
    'public_password_label'  => 'Password protect (optional)',
    'public_password_placeholder' => 'Leave blank to keep current password',
    'public_url'             => 'Shareable link',
    'public_sections'        => 'Sections to show',

    // Section labels
    'section_chart'       => 'Chart',
    'section_pages'       => 'Top Pages',
    'section_sources'     => 'Traffic Sources',
    'section_locations'   => 'Locations',
    'section_devices'     => 'Devices',
    'section_browsers'    => 'Browsers',
    'section_os'          => 'Operating Systems',
    'section_resolutions' => 'Screen Resolutions',

    // Stats on index
    'stats_today'      => 'Today',
    'stats_this_month' => 'This month',
    'stats_last_month' => 'Last month',
    'stats_hits'       => 'hits',

    // Stats cards on index
    'card_total_visitors'  => 'Total Visitors',
    'card_total_sessions'  => 'Total Sessions',
    'card_total_pageviews' => 'Total Pageviews',
    'card_sites_tracked'   => 'Sites Tracked',
    'vs_previous'          => 'vs previous',
    'your_websites'        => 'Your Websites',
    'visitors'             => 'Visitors',
    'sessions'             => 'Sessions',
    'pageviews'            => 'Pageviews',
    'tracking_paused'      => 'Tracking paused',
    'site_settings'        => 'Site settings',
    'open_dashboard'       => 'Open :site dashboard',

    // Plan usage
    'plan_usage'         => 'Plan usage',
    'billing_period'     => 'Billing period: :from – :to',
    'day_of_total'       => 'Day :elapsed of :total',
    'pageviews_label'    => 'pageviews',
    'percent_used'       => ':percent% used',
    'unlimited'          => 'Unlimited',
    'resets'             => 'Resets :date',

    // Show page
    'website_details'        => 'Website Details',
    'delete_website_title'   => 'Delete website?',
    'delete_website_warn'    => 'You\'re about to permanently delete :name and all of its analytics data (pageviews, events, errors, heatmaps). This cannot be undone.',
    'confirm_password_label' => 'Confirm your password',
    'confirm_password_placeholder' => 'Your account password',

    // Buttons
    'btn_add_site'    => 'Add Website',
    'btn_save'        => 'Save Website',
    'btn_delete'      => 'Delete Website',
    'btn_view_stats'  => 'View Stats',

    // Confirm
    'confirm_delete' => 'Are you sure you want to delete this website? All analytics data will be retained in ClickHouse.',

    // Empty states
    'no_sites'        => 'No websites added yet.',
    'no_sites_cta'    => 'Add your first website to start tracking.',

    // Success messages
    'msg_added'   => 'Website added. Copy the tracking script below.',
    'msg_updated' => 'Website updated.',
    'msg_removed' => 'Website removed.',

    // Create form placeholders
    'placeholder_name'   => 'My Website',
    'placeholder_domain' => 'example.com',

    // Account picker
    'account_picker_title'   => 'Choose an account',
    'account_picker_logged'  => 'Logged in as :email',
    'account_picker_choose_title' => 'Choose account',
    'account_your'           => 'Your account',
    'account_no_sites_yet'   => 'No sites yet',
    'account_site_one'       => ':count site',
    'account_site_many'      => ':count sites',
    'btn_logout'             => 'Log out',

];
