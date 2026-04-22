<?php

declare(strict_types=1);

use Cog\Laravel\Clickhouse\Migration\AbstractClickhouseMigration;

return new class extends AbstractClickhouseMigration
{
    public function up(): void
    {
        $this->clickhouseClient->write(
            <<<'SQL'
                CREATE TABLE IF NOT EXISTS custom_events (
                    site_id            String              DEFAULT '',
                    timestamp          DateTime            DEFAULT now(),
                    session_id         String              DEFAULT '',
                    visitor_id         String              DEFAULT '',
                    event_name         String              DEFAULT '',
                    properties         String              DEFAULT '',
                    url                String              DEFAULT '',
                    hostname           String              DEFAULT '',
                    country            String              DEFAULT '',
                    device_type        String              DEFAULT ''
                )
                ENGINE = MergeTree()
                PARTITION BY toYYYYMM(timestamp)
                ORDER BY (site_id, toDate(timestamp))
                SQL,
        );
    }
};
