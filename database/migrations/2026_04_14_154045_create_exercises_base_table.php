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
        Schema::create('exercises_base', function (Blueprint $label) {
            $label->id();
            $label->string('name');
            $label->string('muscle_group')->nullable();
            $label->string('equipment')->nullable();
            $label->text('description')->nullable();
            $label->json('instructions')->nullable();
            $label->string('video_url')->nullable();
            $label->boolean('is_public')->default(true);
            $label->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exercises_base');
    }
};
