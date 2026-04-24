<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('goal_completions', function (Blueprint $table) {
            $table->decimal('monetary_value', 10, 2)->default(0)->after('session_id');
        });
    }

    public function down(): void
    {
        Schema::table('goal_completions', function (Blueprint $table) {
            $table->dropColumn('monetary_value');
        });
    }
};
