<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('routine_exercise', function (Blueprint $table) {
            $table->unsignedSmallInteger('rest_seconds')->default(90)->after('superset_group');
        });
    }

    public function down(): void
    {
        Schema::table('routine_exercise', function (Blueprint $table) {
            $table->dropColumn('rest_seconds');
        });
    }
};
