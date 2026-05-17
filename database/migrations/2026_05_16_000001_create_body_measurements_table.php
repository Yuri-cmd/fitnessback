<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('body_measurements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('waist_cm', 5, 1)->nullable();
            $table->decimal('chest_cm', 5, 1)->nullable();
            $table->decimal('left_arm_cm', 5, 1)->nullable();
            $table->decimal('right_arm_cm', 5, 1)->nullable();
            $table->decimal('left_leg_cm', 5, 1)->nullable();
            $table->decimal('right_leg_cm', 5, 1)->nullable();
            $table->decimal('hips_cm', 5, 1)->nullable();
            $table->date('measured_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('body_measurements');
    }
};
