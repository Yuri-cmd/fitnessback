<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AchievementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $achievements = [
            [
                'name' => 'Principiante de Hierro',
                'description' => 'Completa tu primera rutina.',
                'icon' => 'fa-solid fa-medal',
                'type' => 'routine_count',
                'threshold' => 1,
            ],
            [
                'name' => 'Consistencia Pura',
                'description' => 'Logra una racha de 7 días entrenando.',
                'icon' => 'fa-solid fa-fire',
                'type' => 'streak',
                'threshold' => 7,
            ],
            [
                'name' => 'Súper Hidratado',
                'description' => 'Registra 2L de agua en un solo día.',
                'icon' => 'fa-solid fa-droplet',
                'type' => 'water_daily',
                'threshold' => 2000,
            ],
            [
                'name' => 'Maestro del Volumen',
                'description' => 'Mueve un total de 10,000kg en una sesión.',
                'icon' => 'fa-solid fa-dumbbell',
                'type' => 'session_volume',
                'threshold' => 10000,
            ],
            [
                'name' => 'Guerrero de Bronce',
                'description' => 'Completa un total de 10 entrenamientos.',
                'icon' => 'fa-solid fa-shield-halved',
                'type' => 'routine_count',
                'threshold' => 10,
            ],
            [
                'name' => 'Guerrero de Plata',
                'description' => 'Completa un total de 50 entrenamientos.',
                'icon' => 'fa-solid fa-shield-halved',
                'type' => 'routine_count',
                'threshold' => 50,
            ],
            [
                'name' => 'Guerrero de Oro',
                'description' => 'Completa un total de 100 entrenamientos.',
                'icon' => 'fa-solid fa-shield-halved',
                'type' => 'routine_count',
                'threshold' => 100,
            ],
        ];

        foreach ($achievements as $achievement) {
            \App\Models\Achievement::updateOrCreate(['name' => $achievement['name']], $achievement);
        }
    }
}
