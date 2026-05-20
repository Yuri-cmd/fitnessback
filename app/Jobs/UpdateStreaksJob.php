<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\UserStreak;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class UpdateStreaksJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $yesterday = now()->subDay()->toDateString();

        User::with(['streak', 'notificationSetting'])->chunk(100, function ($users) use ($yesterday) {
            foreach ($users as $user) {
                try {
                    $this->updateUserStreak($user, $yesterday);
                } catch (\Throwable $e) {
                    Log::error("UpdateStreaks error for user {$user->id}: " . $e->getMessage());
                }
            }
        });
    }

    private function updateUserStreak(User $user, string $yesterday): void
    {
        $streak = $user->streak ?? UserStreak::firstOrCreate(['user_id' => $user->id]);

        // ── Racha de entrenamiento ────────────────────────────────────────────
        $workedOutYesterday = $user->routineLogs()
            ->whereDate('completed_at', $yesterday)
            ->exists();

        if ($workedOutYesterday) {
            $newWorkoutStreak = $streak->workout_streak + 1;
            $streak->workout_streak = $newWorkoutStreak;
            if ($newWorkoutStreak > $streak->best_workout_streak) {
                $streak->best_workout_streak = $newWorkoutStreak;
            }
            $streak->last_workout_date = $yesterday;
        } else {
            // Solo resetear si la racha era mayor a 0 (evita writes innecesarios)
            if ($streak->workout_streak > 0) {
                $streak->workout_streak = 0;
            }
        }

        // ── Racha de agua ─────────────────────────────────────────────────────
        $goalGlasses  = $user->notificationSetting?->water_goal_glasses ?? 8;
        $goalMl       = $goalGlasses * 250;
        $yesterdayMl  = (int) $user->waterLogs()
            ->whereDate('logged_at', $yesterday)
            ->sum('amount_ml');

        if ($yesterdayMl >= $goalMl) {
            $newWaterStreak = $streak->water_streak + 1;
            $streak->water_streak = $newWaterStreak;
            if ($newWaterStreak > $streak->best_water_streak) {
                $streak->best_water_streak = $newWaterStreak;
            }
            $streak->last_water_date = $yesterday;
        } else {
            if ($streak->water_streak > 0) {
                $streak->water_streak = 0;
            }
        }

        $streak->save();
    }
}
