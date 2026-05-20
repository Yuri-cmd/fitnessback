<?php

namespace App\Jobs;

use App\Models\NotificationMessage;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendEveningMotivationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(NotificationService $notifications): void
    {
        $currentTime = now()->format('H:i');
        $today       = now()->toDateString();

        $users = User::query()
            ->whereHas('notificationSetting', function ($q) use ($currentTime) {
                $q->where('evening_motivation_enabled', true)
                  ->where('evening_motivation_time', $currentTime);
            })
            ->whereHas('fcmTokens')
            ->get();

        foreach ($users as $user) {
            try {
                $firstName    = explode(' ', $user->name)[0];
                $trainedToday = $user->routineLogs()->whereDate('completed_at', $today)->exists();

                if ($trainedToday) {
                    $template = NotificationMessage::random('motivacion', 'noche_entrenado');
                    $title    = "¡Excelente trabajo, {$firstName}! 🌙";
                    $body     = $template?->body ?? '¡Increíble esfuerzo hoy! 🏆 Descansa bien, mañana volvemos más fuertes.';
                } else {
                    $template = NotificationMessage::random('motivacion', 'noche_no_entrenado');
                    $title    = "¡Termina el día fuerte, {$firstName}! 🌙";
                    $body     = $template?->body ?? 'El día aún no termina. 🌆 Incluso 20 minutos de ejercicio marcan la diferencia.';
                }

                $notifications->sendToUser(
                    $user,
                    $title,
                    $body,
                    ['type' => 'evening_motivation']
                );
            } catch (\Throwable $e) {
                Log::error("EveningMotivation error for user {$user->id}: " . $e->getMessage());
            }
        }
    }
}
