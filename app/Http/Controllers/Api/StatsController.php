<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RoutineLog;
use App\Models\WorkoutSetLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatsController extends Controller
{
    public function weightHistory(Request $request)
    {
        $logs = $request->user()->weightLogs()
            ->orderBy('created_at', 'asc')
            ->take(15)
            ->get(['weight', 'created_at']);

        return response()->json($logs);
    }

    public function volumeByMuscle(Request $request)
    {
        $data = WorkoutSetLog::where('workout_set_logs.user_id', $request->user()->id)
            ->where('workout_set_logs.logged_at', '>=', now()->subDays(30))
            ->join('exercises_base', 'workout_set_logs.exercise_id', '=', 'exercises_base.id')
            ->select('exercises_base.muscle_group', DB::raw('SUM(reps_done * weight_kg) as total_volume'))
            ->groupBy('exercises_base.muscle_group')
            ->orderByDesc('total_volume')
            ->get();

        return response()->json($data);
    }

    public function activityHeatmap(Request $request)
    {
        $logs = RoutineLog::where('user_id', $request->user()->id)
            ->whereYear('completed_at', now()->year)
            ->select(DB::raw('DATE(completed_at) as date'), DB::raw('COUNT(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json($logs);
    }

    public function personalRecords(Request $request)
    {
        $records = WorkoutSetLog::where('workout_set_logs.user_id', $request->user()->id)
            ->join('exercises_base', 'workout_set_logs.exercise_id', '=', 'exercises_base.id')
            ->select(
                'exercises_base.name',
                'exercises_base.muscle_group',
                DB::raw('MAX(weight_kg) as max_weight'),
                DB::raw('MAX(reps_done) as max_reps')
            )
            ->groupBy('exercises_base.name', 'exercises_base.muscle_group')
            ->orderByDesc('max_weight')
            ->limit(10)
            ->get();

        return response()->json($records);
    }
}
