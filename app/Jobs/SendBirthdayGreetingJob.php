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

class SendBirthdayGreetingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(NotificationService $notifications): void
    {
        $todayMonth = now()->month;
        $todayDay   = now()->day;

        $users = User::query()
            ->whereHas('notificationSetting', function ($q) {
                $q->where('birthday_notification_enabled', true);
            })
            ->whereHas('profile', function ($q) use ($todayMonth, $todayDay) {
                $q->whereMonth('birth_date', $todayMonth)
                  ->whereDay('birth_date', $todayDay);
            })
            ->whereHas('fcmTokens')
            ->with('profile')
            ->get();

        foreach ($users as $user) {
            try {
                $firstName = explode(' ', $user->name)[0];
                $age       = $user->profile?->age;
                $ageText   = $age ? " ¡{$age} años!" : '';
                $template  = NotificationMessage::random('cumpleanos');
                $body      = $template?->body ?? 'Que este nuevo año de vida esté lleno de metas alcanzadas y récords personales. 🏋️🎂';

                $notifications->sendToUser(
                    $user,
                    "¡Feliz cumpleaños, {$firstName}!{$ageText} 🎂🎉",
                    $body,
                    ['type' => 'birthday_greeting']
                );
            } catch (\Throwable $e) {
                Log::error("BirthdayGreeting error for user {$user->id}: " . $e->getMessage());
            }
        }
    }
}
