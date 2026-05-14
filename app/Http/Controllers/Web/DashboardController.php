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

        $logs = RoutineLog::where('user_id', $user->id)
            ->whereBetween('completed_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->get();

        $weeklyProgress = [];
        for ($i = 1; $i <= 7; $i++) {
            $weeklyProgress[$i] = $logs->contains(fn ($log) => $log->completed_at->dayOfWeekIso == $i);
        }

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

        return view('dashboard.index', compact('user', 'profile', 'bmi', 'bmiCategory', 'weightToLose', 'weeklyProgress', 'weightTrend'));
    }

    public function updateProfile(Request $request)
    {
        $data = $request->validate([
            'height'         => 'nullable|numeric|min:50|max:300',
            'current_weight' => 'nullable|numeric|min:20|max:300',
            'goal_weight'    => 'nullable|numeric|min:20|max:300',
        ]);

        $profile = Auth::user()->profile ?? Auth::user()->profile()->create([]);
        $profile->update(array_filter($data, fn ($v) => $v !== null));

        return back()->with('success', 'Perfil actualizado correctamente.');
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
