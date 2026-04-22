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

    // Public dashboard
    'public_title'           => 'Public Dashboard',
    'public_enable'          => 'Make this dashboard publicly accessible',
    'public_password_label'  => 'Password protect (optional)',
    'public_password_placeholder' => 'Leave blank to keep current password',
    'public_url'             => 'Shareable link',
    'public_sections'        => 'Sections to show',

    // Stats on index
    'stats_today'      => 'Today',
    'stats_this_month' => 'This month',
    'stats_last_month' => 'Last month',
    'stats_hits'       => 'hits',

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

];
