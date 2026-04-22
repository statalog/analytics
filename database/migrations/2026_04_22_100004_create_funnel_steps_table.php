<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('funnel_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('funnel_id')->constrained()->cascadeOnDelete();
            $table->string('label');
            $table->string('path');
            $table->unsignedInteger('step_order');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('funnel_steps');
    }
};
