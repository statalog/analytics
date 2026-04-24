<?php

declare(strict_types=1);

use Cog\Laravel\Clickhouse\Migration\AbstractClickhouseMigration;

return new class extends AbstractClickhouseMigration
{
    public function up(): void
    {
        $cols = [
            'network_time'        => 'UInt32',
            'server_time'         => 'UInt32',
            'transfer_time'       => 'UInt32',
            'dom_processing_time' => 'UInt32',
            'dom_completion_time' => 'UInt32',
            'on_load_time'        => 'UInt32',
        ];

        foreach ($cols as $col => $type) {
            $this->clickhouseClient->write(
                "ALTER TABLE pageviews ADD COLUMN IF NOT EXISTS {$col} {$type} DEFAULT 0"
            );
        }
    }
};
