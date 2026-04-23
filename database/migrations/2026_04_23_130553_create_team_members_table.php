<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('team_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('role', 16)->default('viewer'); // admin | viewer
            $table->timestamps();

            $table->unique(['owner_id', 'user_id']);
            $table->index('user_id');
        });

        Schema::create('team_member_site_access', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_member_id')->constrained('team_members')->cascadeOnDelete();
            $table->foreignId('site_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['team_member_id', 'site_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('team_member_site_access');
        Schema::dropIfExists('team_members');
    }
};
