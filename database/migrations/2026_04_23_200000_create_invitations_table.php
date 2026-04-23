<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invitations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $table->string('email');
            $table->string('role', 16)->default('viewer');
            $table->text('sites_json')->nullable(); // null = all sites; JSON array of site IDs otherwise
            $table->string('token', 64)->unique();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index('email');
            $table->index('owner_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invitations');
    }
};
