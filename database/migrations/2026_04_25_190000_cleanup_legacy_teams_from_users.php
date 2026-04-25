<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Drop the Jetstream/Teams FK and column from users if they were left behind.
        if (Schema::hasColumn('users', 'current_team_id')) {
            Schema::table('users', function (Blueprint $table) {
                $fks = collect(DB::select(
                    "SELECT CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS
                     WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users'
                       AND CONSTRAINT_TYPE = 'FOREIGN KEY'"
                ))->pluck('CONSTRAINT_NAME');

                if ($fks->contains('users_current_team_id_foreign')) {
                    $table->dropForeign('users_current_team_id_foreign');
                }

                $table->dropColumn('current_team_id');
            });
        }

        Schema::dropIfExists('team_user');
        Schema::dropIfExists('team_invitations');
        Schema::dropIfExists('teams');
    }

    public function down(): void
    {
        // Intentionally not restoring — these were orphaned Jetstream remnants.
    }
};
