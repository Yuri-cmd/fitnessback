<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendWaterReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(NotificationService $notifications): void
    {
        $currentTime = now()->format('H:i');
        $today       = now()->toDateString();

        // Usuarios con recordatorio de agua activo que tienen este horario configurado
        $users = User::query()
            ->whereHas('notificationSetting', function ($q) use ($currentTime) {
                $q->where('water_reminder_enabled', true)
                  ->whereJsonContains('water_reminder_times', $currentTime);
            })
            ->whereHas('fcmTokens')
            ->get();

        foreach ($users as $user) {
            try {
                $setting      = $user->notificationSetting;
                $goalGlasses  = $setting->water_goal_glasses ?? 8;
                $totalMl      = (int) $user->waterLogs()
                                    ->whereDate('logged_at', $today)
                                    ->sum('amount_ml');
                $glassesLogged = (int) floor($totalMl / 250);

                // No enviar si ya alcanzó la meta
                if ($glassesLogged >= $goalGlasses) {
                    continue;
                }

                $remaining = $goalGlasses - $glassesLogged;
                $body = $glassesLogged === 0
                    ? "¡Recuerda hidratarte! Tu meta es {$goalGlasses} vasos hoy. 💧"
                    : "Ya llevas {$glassesLogged}/{$goalGlasses} vasos. ¡Te faltan {$remaining} más! 💧";

                $notifications->sendToUser(
                    $user,
                    '¡Hora de tomar agua!',
                    $body,
                    ['type' => 'water_reminder']
                );
            } catch (\Throwable $e) {
                Log::error("WaterReminder error for user {$user->id}: " . $e->getMessage());
            }
        }
    }
}
