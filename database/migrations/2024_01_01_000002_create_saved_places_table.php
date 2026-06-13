<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('saved_places', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');                        // اسم المكان
            $table->json('grid_data');                     // شبكة الإحداثيات
            $table->float('area_width')->default(0);       // عرض المنطقة بالمتر
            $table->float('area_height')->default(0);      // ارتفاع المنطقة بالمتر
            $table->boolean('is_temporary')->default(false);
            $table->timestamps();

            // كحد أقصى 3 أماكن لكل مستخدم — يُطبَّق في Controller
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('saved_places');
    }
};
