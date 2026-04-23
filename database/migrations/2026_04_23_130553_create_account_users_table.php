<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // An account_user row means: "user_id has access to owner_id's account".
        Schema::create('account_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('role', 16)->default('viewer'); // admin | viewer
            $table->timestamps();

            $table->unique(['owner_id', 'user_id']);
            $table->index('user_id');
        });

        // Optional per-site viewer restriction. Empty = all owner's sites.
        Schema::create('account_user_sites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_user_id')->constrained('account_users')->cascadeOnDelete();
            $table->foreignId('site_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['account_user_id', 'site_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('account_user_sites');
        Schema::dropIfExists('account_users');
    }
};
