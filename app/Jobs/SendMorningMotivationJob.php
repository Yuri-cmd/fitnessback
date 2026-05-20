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

class SendMorningMotivationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(NotificationService $notifications): void
    {
        $currentTime = now()->format('H:i');

        $users = User::query()
            ->whereHas('notificationSetting', function ($q) use ($currentTime) {
                $q->where('morning_motivation_enabled', true)
                  ->where('morning_motivation_time', $currentTime);
            })
            ->whereHas('fcmTokens')
            ->get();

        foreach ($users as $user) {
            try {
                $firstName = explode(' ', $user->name)[0];
                $template  = NotificationMessage::random('motivacion', 'manana');
                $body      = $template?->body ?? '¡Hoy es un día perfecto para entrenar! 🔥 Tu yo del futuro te lo agradecerá.';

                $notifications->sendToUser(
                    $user,
                    "¡Buenos días, {$firstName}! 🌤️",
                    $body,
                    ['type' => 'morning_motivation']
                );
            } catch (\Throwable $e) {
                Log::error("MorningMotivation error for user {$user->id}: " . $e->getMessage());
            }
        }
    }
}
