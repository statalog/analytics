<?php

return [

    // Pages
    'page_dashboard'        => 'Dashboard',
    'page_live_stats'       => 'Live Stats',
    'page_campaigns'        => 'Campaigns',
    'page_entry_exit'       => 'Entry & Exit Pages',
    'page_new_vs_returning' => 'New vs Returning',
    'page_time_on_page'     => 'Time on Page',
    'page_visit_depth'      => 'Visit Depth',
    'page_custom_events'    => 'Custom Events',
    'page_event_detail'     => 'Event Detail',
    'page_funnels'          => 'Funnels',
    'page_create_funnel'    => 'Create Funnel',
    'page_edit_funnel'      => 'Edit Funnel',
    'page_funnel_report'    => 'Funnel Report',
    'page_goals'            => 'Goals',
    'page_create_goal'      => 'Create Goal',
    'page_edit_goal'        => 'Edit Goal',
    'page_goal_report'      => 'Goal Report',
    'page_ai_insights'      => 'AI Insights',

    // Date ranges
    'range_live'         => 'Live',
    'range_today'        => 'Today',
    'range_yesterday'    => 'Yesterday',
    'range_last_24h'     => 'Last 24 hours',
    'range_last_7_days'  => 'Last 7 days',
    'range_last_30_days' => 'Last 30 days',
    'range_this_month'   => 'This month',
    'range_last_month'   => 'Last month',
    'range_custom'       => 'Custom range',

    // Stat cards
    'card_top_pages'          => 'Top Pages',
    'card_traffic_sources'    => 'Traffic Sources',
    'card_locations'          => 'Locations',
    'card_devices'            => 'Devices',
    'card_browsers'           => 'Browsers',
    'card_operating_systems'  => 'Operating Systems',
    'card_screen_resolutions' => 'Screen Resolutions',

    // Charts
    'chart_traffic_overview' => 'Traffic Overview',
    'trend_over_time'        => 'Trend Over Time',

    // Table columns
    'col_page'              => 'Page',
    'col_page_url'          => 'Page URL',
    'col_pages'             => 'Pages',
    'col_pageviews'         => 'Pageviews',
    'col_total_pageviews'   => 'Total Pageviews',
    'col_visitors'          => 'Visitors',
    'col_unique_visitors'   => 'Unique Visitors',
    'col_visits'            => 'Visits',
    'col_sessions'          => 'Sessions',
    'col_bounce_rate'       => 'Bounce Rate',
    'col_avg_duration'      => 'Avg Duration',
    'col_avg_time'          => 'Avg Time',
    'col_source'            => 'Source',
    'col_medium'            => 'Medium',
    'col_campaign'          => 'Campaign',
    'col_exits'             => 'Exits',
    'col_segment'           => 'Segment',
    'col_event_name'        => 'Event Name',
    'col_total_occurrences' => 'Total Occurrences',
    'col_first_seen'        => 'First Seen',
    'col_last_seen'         => 'Last Seen',
    'col_properties'        => 'Properties',
    'col_count'             => 'Count',
    'col_completions'       => 'Completions',
    'col_target_path'       => 'Target Path',
    'col_match_type'        => 'Match Type',

    // Labels
    'label_new'            => 'New',
    'label_returning'      => 'Returning',
    'label_actions'        => 'Actions',
    'label_name'           => 'Name',
    'label_funnel_name'    => 'Funnel Name',
    'label_goal_name'      => 'Goal Name',
    'label_target_path'    => 'Target Path',
    'label_match_type'     => 'Match Type',
    'label_monetary_value' => 'Monetary Value',
    'label_steps'          => 'Steps',
    'label_steps_hint'     => 'Add the URLs visitors must go through, in order.',
    'metric_visitors'      => 'Visitors',

    // Match types
    'match_exact'       => 'Exact',
    'match_contains'    => 'Contains',
    'match_starts_with' => 'Starts with',

    // Buttons
    'btn_cancel'               => 'Cancel',
    'btn_report'               => 'View Report',
    'btn_add_step'             => 'Add Step',
    'btn_create_funnel'        => 'Create Funnel',
    'btn_create_funnel_submit' => 'Create Funnel',
    'btn_update_funnel'        => 'Update Funnel',
    'btn_create_goal'          => 'Create Goal',
    'btn_create_goal_submit'   => 'Create Goal',
    'btn_update_goal'          => 'Update Goal',
    'btn_generate_insight'     => 'Generate Insight',

    // Placeholders
    'placeholder_search_url'  => 'Search URL...',
    'placeholder_funnel_name' => 'e.g. Checkout Funnel',
    'placeholder_goal_name'   => 'e.g. Contact Form Submission',
    'placeholder_target_path' => 'e.g. /thank-you',
    'placeholder_step_label'  => 'Step label (optional)',
    'placeholder_step_path'   => '/page-url',

    // Hints
    'hint_target_path' => 'The URL path visitors must reach to complete this goal.',

    // Confirm dialogs
    'confirm_delete_funnel' => 'Are you sure you want to delete this funnel?',
    'confirm_delete_goal'   => 'Are you sure you want to delete this goal?',

    // Funnel report
    'funnel_visitors'           => 'Visitors',
    'funnel_dropped'            => 'Dropped',
    'funnel_overall_conversion' => 'Overall Conversion',
    'funnel_steps_count'        => 'Steps',

    // Goal report
    'goal_total_completions' => 'Total Completions',

    // Events
    'event_occurrences_over_time' => 'Occurrences Over Time',
    'event_properties'            => 'Event Properties',

    // Live stats
    'live_visitors_online'     => 'visitors online',
    'live_visitors_in_30_min'  => 'Visitors in last 30 min',
    'live_visitors_in_60_min'  => 'Visitors in last 60 min',
    'live_visitors_per_minute' => 'Visitors per minute',
    'live_recent_visits'       => 'Recent Visits',
    'live_no_recent_visits'    => 'No recent visits',
    'live_col_time'            => 'Time',
    'live_col_page'            => 'Page',
    'live_col_location'        => 'Location',
    'live_col_device'          => 'Device',
    'live_col_browser'         => 'Browser',
    'live_col_source'          => 'Source',
    'live_source_direct'       => 'Direct',

    // Visit depth
    'visit_depth_distribution' => 'Visit Depth Distribution',

    // Entry/Exit tabs
    'tab_entry_pages' => 'Entry Pages',
    'tab_exit_pages'  => 'Exit Pages',

    // AI Insights
    'ai_insights_enabled'        => 'AI Insights Enabled',
    'ai_insights_daily_report'   => 'Generate daily reports',
    'ai_insights_weekly_report'  => 'Generate weekly reports',
    'ai_insights_monthly_report' => 'Generate monthly reports',
    'ai_insights_report_email'   => 'Send reports to email',
    'ai_insights_no_reports'     => 'No reports generated yet.',

    // Empty states
    'loading'            => 'Loading...',
    'no_data'            => 'No data',
    'no_data_available'  => 'No data available',
    'no_data_period'     => 'No data for this period',
    'no_campaign_data'   => 'No campaign data',
    'no_events'          => 'No custom events recorded yet.',
    'no_funnels'         => 'No funnels created yet.',
    'no_goals'           => 'No goals created yet.',
    'no_properties'      => 'No properties recorded.',
    'unknown'            => 'Unknown',

];
