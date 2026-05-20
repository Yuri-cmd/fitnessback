<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->boolean('workout_reminder_enabled')->default(true);
            $table->string('workout_reminder_time')->default('20:00'); // HH:MM UTC
            $table->boolean('water_reminder_enabled')->default(true);
            // JSON array of HH:MM strings — sin default aquí, MySQL no lo permite en JSON
            $table->json('water_reminder_times')->nullable();
            $table->unsignedInteger('water_goal_glasses')->default(8);
            $table->timestamps();

            $table->unique('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_settings');
    }
};
