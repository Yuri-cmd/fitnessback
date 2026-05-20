<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_messages', function (Blueprint $table) {
            $table->id();
            // Categoría principal: agua | recordatorio | motivacion | cumpleanos
            $table->string('type', 30)->index();
            // Subtipo para diferenciar variantes dentro del mismo type:
            // agua        → cero_vasos | progreso
            // recordatorio → sin_racha | con_racha
            // motivacion  → manana | noche_entrenado | noche_no_entrenado
            // cumpleanos  → (null)
            $table->string('context', 30)->nullable()->index();
            // Soporta placeholders: {meta} {vasos} {faltan} {racha} {dias}
            $table->text('body');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_messages');
    }
};
