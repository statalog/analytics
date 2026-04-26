<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Bot detection is now automatic for every site. Backfill the legacy
        // `track_bots` column so any code path still gating on it (or any
        // future read for stats) reflects the new always-on behaviour.
        DB::table('sites')->update(['track_bots' => true]);
    }

    public function down(): void
    {
        // Intentionally a no-op — we don't know which sites had it disabled.
    }
};
