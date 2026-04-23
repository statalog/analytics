<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ga_historical_daily', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->unsignedInteger('visitors')->default(0);
            $table->unsignedInteger('pageviews')->default(0);
            $table->unsignedInteger('sessions')->default(0);
            $table->decimal('bounce_rate', 5, 2)->default(0);
            $table->unsignedInteger('avg_duration')->default(0);
            $table->timestamps();

            $table->unique(['site_id', 'date']);
        });

        Schema::create('ga_historical_pages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained()->cascadeOnDelete();
            $table->string('page_path', 500);
            $table->unsignedBigInteger('pageviews')->default(0);
            $table->unsignedInteger('rank')->default(0);
            $table->timestamps();

            $table->index(['site_id', 'rank']);
        });

        Schema::create('ga_historical_sources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained()->cascadeOnDelete();
            $table->string('source')->nullable();
            $table->unsignedBigInteger('visitors')->default(0);
            $table->unsignedInteger('rank')->default(0);
            $table->timestamps();

            $table->index(['site_id', 'rank']);
        });

        Schema::create('ga_historical_countries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained()->cascadeOnDelete();
            $table->string('country')->nullable();
            $table->unsignedBigInteger('visitors')->default(0);
            $table->unsignedInteger('rank')->default(0);
            $table->timestamps();

            $table->index(['site_id', 'rank']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ga_historical_countries');
        Schema::dropIfExists('ga_historical_sources');
        Schema::dropIfExists('ga_historical_pages');
        Schema::dropIfExists('ga_historical_daily');
    }
};
