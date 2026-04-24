<?php

declare(strict_types=1);

use Cog\Laravel\Clickhouse\Migration\AbstractClickhouseMigration;

return new class extends AbstractClickhouseMigration
{
    public function up(): void
    {
        $this->clickhouseClient->write(
            "ALTER TABLE pageviews ADD COLUMN IF NOT EXISTS latitude  Float32 DEFAULT 0"
        );
        $this->clickhouseClient->write(
            "ALTER TABLE pageviews ADD COLUMN IF NOT EXISTS longitude Float32 DEFAULT 0"
        );
    }
};
