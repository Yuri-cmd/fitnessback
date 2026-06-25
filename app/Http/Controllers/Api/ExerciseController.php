<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ExerciseBase;
use Illuminate\Http\Request;

class ExerciseController extends Controller
{
    public function index()
    {
        return ExerciseBase::all();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'muscle_group' => 'nullable|string',
        ]);

        $exercise = ExerciseBase::create($validated);

        return response()->json($exercise, 201);
    }

    public function update(Request $request, ExerciseBase $exercise)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'muscle_group' => 'nullable|string',
        ]);

        $exercise->update($validated);

        return response()->json($exercise);
    }

    public function lastWeights(Request $request)
    {
        $ids = array_filter((array) $request->query('ids', []), 'is_numeric');
        if (empty($ids)) return response()->json((object) []);

        $weights = \App\Models\WorkoutSetLog::where('user_id', $request->user()->id)
            ->whereIn('exercise_id', $ids)
            ->where('weight_kg', '>', 0)
            ->orderByDesc('logged_at')
            ->get(['exercise_id', 'weight_kg'])
            ->unique('exercise_id')
            ->mapWithKeys(fn($log) => [(string) $log->exercise_id => (float) $log->weight_kg]);

        return response()->json($weights);
    }
}
