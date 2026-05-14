<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\RoutineLog;
use App\Models\WeightLog;
use App\Models\WorkoutSetLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StatsController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // 1. Historial de Peso (Últimos 15 registros)
        $weightHistory = WeightLog::where('user_id', $user->id)
            ->orderBy('logged_at', 'asc')
            ->take(15)
            ->get();

        // 2. Volumen por Grupo Muscular (Últimos 30 días)
        $volumeByMuscle = WorkoutSetLog::where('user_id', $user->id)
            ->where('logged_at', '>=', now()->subDays(30))
            ->join('exercises_base', 'workout_set_logs.exercise_id', '=', 'exercises_base.id')
            ->select('exercises_base.muscle_group', DB::raw('SUM(reps_done * weight_kg) as total_volume'))
            ->groupBy('exercises_base.muscle_group')
            ->get();

        // 3. Actividad para Heatmap (Días entrenados este año)
        $activity = RoutineLog::where('user_id', $user->id)
            ->where('completed_at', '>=', now()->startOfYear())
            ->select(DB::raw('DATE(completed_at) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->get()
            ->pluck('count', 'date');

        // 4. Records Personales (PRs)
        $prs = WorkoutSetLog::where('user_id', $user->id)
            ->join('exercises_base', 'workout_set_logs.exercise_id', '=', 'exercises_base.id')
            ->select('exercises_base.name', DB::raw('MAX(weight_kg) as max_weight'))
            ->groupBy('exercises_base.name')
            ->orderBy('max_weight', 'desc')
            ->take(10)
            ->get();

        return view('stats.index', compact('weightHistory', 'volumeByMuscle', 'activity', 'prs'));
    }
}
