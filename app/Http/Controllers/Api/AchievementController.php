<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Achievement;
use App\Models\RoutineLog;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AchievementController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $this->sync($user);

        return response()->json($user->achievements()->withPivot('earned_at')->get());
    }

    private function sync($user): void
    {
        $routineCount = RoutineLog::where('user_id', $user->id)->count();
        $streak       = $this->calcStreak($user->id);
        $waterToday   = $user->waterLogs()
            ->whereDate('logged_at', now()->toDateString())
            ->sum('amount_ml');

        $earnedIds = $user->achievements()->pluck('achievements.id')->toArray();

        foreach (Achievement::all() as $ach) {
            if (in_array($ach->id, $earnedIds)) continue;

            $qualify = match ($ach->type) {
                'routine_count' => $routineCount >= $ach->threshold,
                'streak'        => $streak >= $ach->threshold,
                'water_daily'   => $waterToday >= $ach->threshold,
                default         => false,
            };

            if ($qualify) {
                $user->achievements()->attach($ach->id, ['earned_at' => now()]);
            }
        }
    }

    private function calcStreak(int $userId): int
    {
        $dates = RoutineLog::where('user_id', $userId)
            ->orderByDesc('completed_at')
            ->pluck('completed_at')
            ->map(fn($d) => $d->toDateString())
            ->unique()
            ->values();

        if ($dates->isEmpty()) return 0;

        $first = Carbon::parse($dates->first());
        if ($first->diffInDays(now()) > 1) return 0;

        $streak  = 0;
        $current = null;

        foreach ($dates as $date) {
            $d = Carbon::parse($date);
            if ($streak === 0) {
                $streak  = 1;
                $current = $d;
                continue;
            }
            if ($current->diffInDays($d) === 1) {
                $streak++;
                $current = $d;
            } else {
                break;
            }
        }

        return $streak;
    }
}
