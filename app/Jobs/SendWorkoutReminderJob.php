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

class SendWorkoutReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(NotificationService $notifications): void
    {
        $currentTime = now()->format('H:i');
        $today       = now()->toDateString();

        $users = User::query()
            ->whereHas('notificationSetting', function ($q) use ($currentTime) {
                $q->where('workout_reminder_enabled', true)
                  ->where('workout_reminder_time', $currentTime);
            })
            ->whereHas('fcmTokens')
            ->whereDoesntHave('routineLogs', function ($q) use ($today) {
                $q->whereDate('completed_at', $today);
            })
            ->get();

        foreach ($users as $user) {
            try {
                $streak = $user->streak?->workout_streak ?? 0;

                if ($streak > 0) {
                    $days     = $streak === 1 ? 'día' : 'días';
                    $template = NotificationMessage::random('recordatorio', 'con_racha');
                    $body     = str_replace(
                        ['{racha}', '{dias}'],
                        [$streak, $days],
                        $template?->body ?? "¡No pierdas tu racha de {$streak} {$days}! Entrena hoy para mantenerla. 💪"
                    );
                } else {
                    $template = NotificationMessage::random('recordatorio', 'sin_racha');
                    $body     = $template?->body ?? '¡Es hora de entrenar! Un día más cerca de tus metas. 💪';
                }

                $notifications->sendToUser(
                    $user,
                    '¿Ya entrenaste hoy?',
                    $body,
                    ['type' => 'workout_reminder']
                );
            } catch (\Throwable $e) {
                Log::error("WorkoutReminder error for user {$user->id}: " . $e->getMessage());
            }
        }
    }
}
