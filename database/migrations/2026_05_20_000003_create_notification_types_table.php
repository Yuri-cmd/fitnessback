<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_types', function (Blueprint $table) {
            $table->id();
            $table->string('type', 30);
            $table->string('context', 30)->nullable();
            $table->string('label', 100);
            $table->timestamps();

            $table->unique(['type', 'context']);
        });

        // Carga inicial de los tipos disponibles
        DB::table('notification_types')->insert([
            ['type' => 'agua',        'context' => 'cero_vasos',          'label' => 'Agua - Sin vasos registrados',     'created_at' => now(), 'updated_at' => now()],
            ['type' => 'agua',        'context' => 'progreso',            'label' => 'Agua - En progreso',               'created_at' => now(), 'updated_at' => now()],
            ['type' => 'recordatorio','context' => 'sin_racha',           'label' => 'Recordatorio - Sin racha',         'created_at' => now(), 'updated_at' => now()],
            ['type' => 'recordatorio','context' => 'con_racha',           'label' => 'Recordatorio - Con racha activa',  'created_at' => now(), 'updated_at' => now()],
            ['type' => 'motivacion',  'context' => 'manana',              'label' => 'Motivación - Buenos días',         'created_at' => now(), 'updated_at' => now()],
            ['type' => 'motivacion',  'context' => 'noche_entrenado',     'label' => 'Motivación - Noche (entrenó hoy)', 'created_at' => now(), 'updated_at' => now()],
            ['type' => 'motivacion',  'context' => 'noche_no_entrenado',  'label' => 'Motivación - Noche (no entrenó)',  'created_at' => now(), 'updated_at' => now()],
            ['type' => 'cumpleanos',  'context' => null,                  'label' => 'Cumpleaños',                       'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_types');
    }
};
