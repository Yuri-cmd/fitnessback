<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('routine_exercise', function (Blueprint $table) {
            $table->unsignedTinyInteger('superset_group')->nullable()->after('warmup_reps');
        });
    }

    public function down(): void
    {
        Schema::table('routine_exercise', function (Blueprint $table) {
            $table->dropColumn('superset_group');
        });
    }
};
