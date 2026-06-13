<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('game_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('place_id')->nullable()->constrained('saved_places')->onDelete('set null');
            $table->enum('mode', ['casual', 'ranked'])->default('casual');
            $table->integer('score')->default(0);
            $table->integer('waves_survived')->default(0);
            $table->integer('resources_collected')->default(0);
            $table->integer('duration_seconds')->default(0);
            $table->enum('status', ['active', 'completed', 'abandoned'])->default('active');
            $table->timestamp('ended_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('game_sessions');
    }
};
