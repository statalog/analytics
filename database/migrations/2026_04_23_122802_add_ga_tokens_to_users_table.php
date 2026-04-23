<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('ga_access_token')->nullable()->after('two_factor_confirmed_at');
            $table->text('ga_refresh_token')->nullable()->after('ga_access_token');
            $table->timestamp('ga_token_expires_at')->nullable()->after('ga_refresh_token');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['ga_access_token', 'ga_refresh_token', 'ga_token_expires_at']);
        });
    }
};
