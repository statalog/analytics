<?php

declare(strict_types=1);

use Cog\Laravel\Clickhouse\Migration\AbstractClickhouseMigration;

return new class extends AbstractClickhouseMigration
{
    public function up(): void
    {
        $this->clickhouseClient->write(
            <<<'SQL'
                CREATE TABLE IF NOT EXISTS js_errors (
                    site_id       String   DEFAULT '',
                    timestamp     DateTime DEFAULT now(),
                    fingerprint   String   DEFAULT '',
                    error_type    String   DEFAULT '',
                    message       String   DEFAULT '',
                    source        String   DEFAULT '',
                    line          UInt32   DEFAULT 0,
                    col           UInt32   DEFAULT 0,
                    stack         String   DEFAULT '',
                    url           String   DEFAULT '',
                    hostname      String   DEFAULT '',
                    browser       String   DEFAULT '',
                    os            String   DEFAULT '',
                    device_type   String   DEFAULT '',
                    country       String   DEFAULT '',
                    visitor_id    String   DEFAULT '',
                    is_bot        UInt8    DEFAULT 0,
                    bot_name      String   DEFAULT ''
                )
                ENGINE = MergeTree()
                PARTITION BY toYYYYMM(timestamp)
                ORDER BY (site_id, fingerprint, toDate(timestamp))
                SQL,
        );
    }
};
