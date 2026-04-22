<?php

declare(strict_types=1);

use Cog\Laravel\Clickhouse\Migration\AbstractClickhouseMigration;

return new class extends AbstractClickhouseMigration
{
    public function up(): void
    {
        $this->clickhouseClient->write(
            <<<'SQL'
                CREATE TABLE IF NOT EXISTS pageviews (
                    site_id            String              DEFAULT '',
                    timestamp          DateTime            DEFAULT now(),
                    session_id         String              DEFAULT '',
                    visitor_id         String              DEFAULT '',
                    hostname           String              DEFAULT '',
                    url                String              DEFAULT '',
                    path               String              DEFAULT '',
                    query_string       String              DEFAULT '',
                    referrer           String              DEFAULT '',
                    referrer_domain    String              DEFAULT '',
                    browser            String              DEFAULT '',
                    browser_version    String              DEFAULT '',
                    os                 String              DEFAULT '',
                    os_version         String              DEFAULT '',
                    device_type        String              DEFAULT '',
                    screen_width       UInt16              DEFAULT 0,
                    screen_height      UInt16              DEFAULT 0,
                    country            String              DEFAULT '',
                    region             String              DEFAULT '',
                    city               String              DEFAULT '',
                    utm_source         String              DEFAULT '',
                    utm_medium         String              DEFAULT '',
                    utm_campaign       String              DEFAULT '',
                    utm_content        String              DEFAULT '',
                    utm_term           String              DEFAULT '',
                    load_time          UInt32              DEFAULT 0,
                    visit_duration     UInt32              DEFAULT 0,
                    is_bounce          UInt8               DEFAULT 1,
                    is_new_visitor     UInt8               DEFAULT 1,
                    entry_page         String              DEFAULT '',
                    exit_page          String              DEFAULT ''
                )
                ENGINE = MergeTree()
                PARTITION BY toYYYYMM(timestamp)
                ORDER BY (site_id, toDate(timestamp))
                SQL,
        );
    }
};
