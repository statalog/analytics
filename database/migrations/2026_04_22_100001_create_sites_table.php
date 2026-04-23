<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('domain');
            $table->string('site_id', 20)->unique();
            $table->string('timezone')->default('UTC');
            $table->boolean('is_active')->default(true);
            $table->boolean('track_subdomains')->default(false);
            $table->boolean('track_bots')->default(false);
            $table->boolean('is_public')->default(false);
            $table->string('public_token', 64)->nullable();
            $table->string('public_password')->nullable();
            $table->text('public_sections')->nullable();
            $table->softDeletes();
            $table->timestamps();
            $table->index('domain');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sites');
    }
};
