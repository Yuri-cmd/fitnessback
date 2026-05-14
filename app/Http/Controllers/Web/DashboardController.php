<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\RoutineLog;
use App\Models\WeightLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user    = Auth::user();
        $profile = $user->profile;

        [$bmi, $bmiCategory, $weightToLose] = $this->calcBmi($profile);

        // Progreso semanal
        $logs = RoutineLog::where('user_id', $user->id)
            ->whereBetween('completed_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->get();

        $weeklyProgress = [];
        for ($i = 1; $i <= 7; $i++) {
            $weeklyProgress[$i] = $logs->contains(fn ($log) => $log->completed_at->dayOfWeekIso == $i);
        }

        // Racha actual (Streak)
        $streak = $this->calcStreak($user->id);

        // Hidratación hoy
        $waterToday = $user->waterLogs()
            ->whereDate('logged_at', now()->toDateString())
            ->sum('amount_ml');

        $this->syncAchievements($user, $streak, $waterToday);

        // Tendencia de peso corporal (últimas 2 mediciones)
        $weightTrend = null;
        $recentWeights = WeightLog::where('user_id', $user->id)
            ->orderBy('logged_at', 'desc')
            ->take(2)
            ->get();

        if ($recentWeights->count() === 2) {
            $diff = round($recentWeights[0]->weight - $recentWeights[1]->weight, 1);
            if ($diff !== 0.0) {
                $weightTrend = [
                    'diff'      => abs($diff),
                    'direction' => $diff < 0 ? 'down' : 'up',
                    'current'   => $recentWeights[0]->weight,
                ];
            }
        }

        return view('dashboard.index', compact(
            'user', 'profile', 'bmi', 'bmiCategory', 'weightToLose', 
            'weeklyProgress', 'weightTrend', 'streak', 'waterToday'
        ));
    }

    public function logWater(Request $request)
    {
        $request->validate(['amount' => 'required|integer|min:50|max:1000']);
        
        Auth::user()->waterLogs()->create([
            'amount_ml' => $request->amount,
            'logged_at' => now(),
        ]);

        return back()->with('success', 'Agua registrada correctamente.');
    }

    public function updateProfile(Request $request)
    {
        $data = $request->validate([
            'height'         => 'nullable|numeric|min:50|max:300',
            'current_weight' => 'nullable|numeric|min:20|max:300',
            'goal_weight'    => 'nullable|numeric|min:20|max:300',
            'birth_date'     => 'nullable|date',
            'gender'         => 'nullable|string|in:male,female',
            'activity_level' => 'nullable|string|in:sedentary,lightly_active,moderately_active,very_active,extra_active',
        ]);

        $profile = Auth::user()->profile ?? Auth::user()->profile()->create([]);
        $profile->update(array_filter($data, fn ($v) => $v !== null));

        return back()->with('success', 'Perfil actualizado correctamente.');
    }

    private function calcStreak($userId)
    {
        $dates = RoutineLog::where('user_id', $userId)
            ->where('completed_at', '<=', now())
            ->orderBy('completed_at', 'desc')
            ->pluck('completed_at')
            ->map(fn($d) => $d->toDateString())
            ->unique();

        if ($dates->isEmpty()) return 0;

        $streak = 0;
        $currentDate = now();
        
        // Check if user worked out today or yesterday to continue streak
        $firstDate = \Carbon\Carbon::parse($dates->first());
        if ($firstDate->diffInDays(now()) > 1) {
            return 0;
        }

        foreach ($dates as $date) {
            $dateObj = \Carbon\Carbon::parse($date);
            if ($streak == 0) {
                $streak = 1;
                $currentDate = $dateObj;
                continue;
            }

            if ($currentDate->diffInDays($dateObj) == 1) {
                $streak++;
                $currentDate = $dateObj;
            } else {
                break;
            }
        }

        return $streak;
    }

    private function syncAchievements($user, $streak, $waterToday)
    {
        $routineCount = RoutineLog::where('user_id', $user->id)->count();
        
        $potentialAchievements = \App\Models\Achievement::all();
        $earnedIds = $user->achievements()->pluck('achievements.id')->toArray();

        foreach ($potentialAchievements as $ach) {
            if (in_array($ach->id, $earnedIds)) continue;

            $earned = false;
            if ($ach->type === 'routine_count' && $routineCount >= $ach->threshold) $earned = true;
            if ($ach->type === 'streak' && $streak >= $ach->threshold) $earned = true;
            if ($ach->type === 'water_daily' && $waterToday >= $ach->threshold) $earned = true;

            if ($earned) {
                $user->achievements()->attach($ach->id, ['earned_at' => now()]);
            }
        }
    }

    private function calcBmi($profile): array
    {
        if (! $profile || ! $profile->height || ! $profile->current_weight) {
            return [null, 'Sin datos', 0];
        }

        $hm  = $profile->height / 100;
        $bmi = round($profile->current_weight / ($hm * $hm), 1);

        $category = match (true) {
            $bmi < 18.5 => 'Bajo peso',
            $bmi < 25   => 'Normal',
            $bmi < 30   => 'Sobrepeso',
            default     => 'Obesidad',
        };

        $idealWeight  = 24.9 * ($hm * $hm);
        $weightToLose = max(0, round($profile->current_weight - $idealWeight, 1));

        return [$bmi, $category, $weightToLose];
    }
}
