<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notification_settings', function (Blueprint $table) {
            $table->boolean('morning_motivation_enabled')->default(true)->after('water_goal_glasses');
            $table->string('morning_motivation_time')->default('07:00')->after('morning_motivation_enabled'); // HH:MM UTC
            $table->boolean('evening_motivation_enabled')->default(true)->after('morning_motivation_time');
            $table->string('evening_motivation_time')->default('21:00')->after('evening_motivation_enabled'); // HH:MM UTC
            $table->boolean('birthday_notification_enabled')->default(true)->after('evening_motivation_time');
        });
    }

    public function down(): void
    {
        Schema::table('notification_settings', function (Blueprint $table) {
            $table->dropColumn([
                'morning_motivation_enabled',
                'morning_motivation_time',
                'evening_motivation_enabled',
                'evening_motivation_time',
                'birthday_notification_enabled',
            ]);
        });
    }
};
