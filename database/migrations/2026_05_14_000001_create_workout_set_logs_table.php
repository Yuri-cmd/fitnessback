<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workout_set_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('routine_log_id')->nullable()->constrained('routine_logs')->nullOnDelete();
            $table->foreignId('exercise_id')->constrained('exercises_base')->cascadeOnDelete();
            $table->unsignedTinyInteger('set_number');
            $table->unsignedSmallInteger('reps_done')->default(0);
            $table->decimal('weight_kg', 6, 2)->nullable();
            $table->timestamp('logged_at')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workout_set_logs');
    }
};
