<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leaderboard', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('season')->default(1);
            $table->integer('rank_points')->default(0);
            $table->integer('wins')->default(0);
            $table->integer('losses')->default(0);
            $table->string('rank_tier')->default('Bronze'); // Bronze/Silver/Gold/Diamond
            $table->timestamps();

            $table->unique(['user_id', 'season']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leaderboard');
    }
};
