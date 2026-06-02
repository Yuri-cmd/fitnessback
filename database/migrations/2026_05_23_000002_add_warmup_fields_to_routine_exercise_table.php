<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('routine_exercise', function (Blueprint $table) {
            // reps_max allows "8-12" rep ranges on working sets (reps = min, reps_max = max)
            $table->integer('reps_max')->nullable()->after('reps');
            $table->integer('warmup_sets')->default(0)->after('sort_order');
            $table->string('warmup_reps', 20)->nullable()->after('warmup_sets');
        });
    }

    public function down(): void
    {
        Schema::table('routine_exercise', function (Blueprint $table) {
            $table->dropColumn(['reps_max', 'warmup_sets', 'warmup_reps']);
        });
    }
};
