<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_streaks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->unsignedInteger('workout_streak')->default(0);
            $table->unsignedInteger('water_streak')->default(0);
            $table->unsignedInteger('best_workout_streak')->default(0);
            $table->unsignedInteger('best_water_streak')->default(0);
            $table->date('last_workout_date')->nullable();
            $table->date('last_water_date')->nullable();
            $table->timestamps();

            $table->unique('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_streaks');
    }
};
