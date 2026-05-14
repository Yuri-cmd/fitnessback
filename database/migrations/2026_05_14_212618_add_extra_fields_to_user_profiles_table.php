<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->date('birth_date')->nullable();
            $table->string('gender')->nullable(); // male, female
            $table->string('activity_level')->nullable(); // sedentary, lightly_active, moderately_active, very_active, extra_active
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->dropColumn(['birth_date', 'gender', 'activity_level']);
        });
    }
};
