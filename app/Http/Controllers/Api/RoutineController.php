<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Routine;
use App\Models\RoutineLog;
use Illuminate\Http\Request;

class RoutineController extends Controller
{
    public function index(Request $request)
    {
        return $request->user()->routines()->with('exercises')->whereNull('archived_at')->get();
    }

    public function archived(Request $request)
    {
        return $request->user()->routines()->with('exercises')->whereNotNull('archived_at')->get();
    }

    public function archive(Request $request, Routine $routine)
    {
        if ($routine->user_id !== $request->user()->id) {
            return response()->json(['message' => 'No autorizado'], 403);
        }
        $routine->update(['archived_at' => now()]);
        return response()->json(['message' => 'Archivada']);
    }

    public function unarchive(Request $request, Routine $routine)
    {
        if ($routine->user_id !== $request->user()->id) {
            return response()->json(['message' => 'No autorizado'], 403);
        }
        $routine->update(['archived_at' => null]);
        return response()->json(['message' => 'Restaurada']);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'exercises' => 'required|array',
            'exercises.*.exercise_id' => 'required|exists:exercises_base,id',
            'exercises.*.sets' => 'required|integer',
            'exercises.*.reps' => 'required|integer',
            'exercises.*.reps_max' => 'nullable|integer',
            'exercises.*.warmup_sets' => 'nullable|integer',
            'exercises.*.warmup_reps' => 'nullable|string|max:20',
            'exercises.*.superset_group' => 'nullable|integer',
            'exercises.*.rest_seconds'   => 'nullable|integer|min:5|max:600',
        ]);

        $routine = $request->user()->routines()->create([
            'name' => $request->name,
        ]);

        foreach ($request->exercises as $index => $ex) {
            $routine->exercises()->attach($ex['exercise_id'], [
                'sets'           => $ex['sets'],
                'reps'           => $ex['reps'],
                'reps_max'       => $ex['reps_max'] ?? null,
                'warmup_sets'    => $ex['warmup_sets'] ?? 0,
                'warmup_reps'    => $ex['warmup_reps'] ?? null,
                'sort_order'     => $index,
                'superset_group' => $ex['superset_group'] ?? null,
                'rest_seconds'   => $ex['rest_seconds'] ?? 90,
            ]);
        }

        return response()->json($routine->load('exercises'), 201);
    }

    public function complete(Request $request, Routine $routine)
    {
        if ($routine->user_id !== $request->user()->id) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $log = RoutineLog::create([
            'user_id'      => $request->user()->id,
            'routine_id'   => $routine->id,
            'completed_at' => now(),
        ]);

        if ($request->has('sets') && is_array($request->sets)) {
            foreach ($request->sets as $set) {
                if (! isset($set['exercise_id'])) continue;
                \App\Models\WorkoutSetLog::create([
                    'user_id'        => $request->user()->id,
                    'routine_log_id' => $log->id,
                    'exercise_id'    => $set['exercise_id'],
                    'set_number'     => $set['set_number'] ?? 1,
                    'reps_done'      => $set['reps_done'] ?? 0,
                    'weight_kg'      => $set['weight_kg'] ?? 0,
                    'logged_at'      => now(),
                ]);
            }
        }

        return response()->json($log);
    }

    public function update(Request $request, Routine $routine)
    {
        if ($routine->user_id !== $request->user()->id) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'exercises' => 'required|array',
            'exercises.*.exercise_id' => 'required|exists:exercises_base,id',
            'exercises.*.sets' => 'required|integer',
            'exercises.*.reps' => 'required|integer',
            'exercises.*.reps_max' => 'nullable|integer',
            'exercises.*.warmup_sets' => 'nullable|integer',
            'exercises.*.warmup_reps' => 'nullable|string|max:20',
            'exercises.*.superset_group' => 'nullable|integer',
            'exercises.*.rest_seconds'   => 'nullable|integer|min:5|max:600',
        ]);

        $routine->update(['name' => $request->name]);

        $syncData = [];
        foreach ($request->exercises as $index => $ex) {
            $syncData[$ex['exercise_id']] = [
                'sets'           => $ex['sets'],
                'reps'           => $ex['reps'],
                'reps_max'       => $ex['reps_max'] ?? null,
                'warmup_sets'    => $ex['warmup_sets'] ?? 0,
                'warmup_reps'    => $ex['warmup_reps'] ?? null,
                'sort_order'     => $index,
                'superset_group' => $ex['superset_group'] ?? null,
                'rest_seconds'   => $ex['rest_seconds'] ?? 90,
            ];
        }
        $routine->exercises()->sync($syncData);

        return response()->json($routine->load('exercises'));
    }

    public function destroy(Request $request, Routine $routine)
    {
        if ($routine->user_id !== $request->user()->id) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $routine->delete();
        return response()->json(['message' => 'Eliminado']);
    }

    public function workoutHistory(Request $request)
    {
        return RoutineLog::where('user_id', $request->user()->id)
            ->with('routine:id,name')
            ->orderBy('completed_at', 'desc')
            ->get();
    }

    public function weeklyProgress(Request $request)
    {
        $startOfWeek = now()->startOfWeek();
        $endOfWeek = now()->endOfWeek();

        $logs = RoutineLog::where('user_id', $request->user()->id)
            ->whereBetween('completed_at', [$startOfWeek, $endOfWeek])
            ->get();

        $progress = [];
        for ($i = 1; $i <= 7; $i++) {
            $hasWorkout = $logs->contains(function ($log) use ($i) {
                return $log->completed_at->dayOfWeekIso == $i;
            });
            $progress[$i] = $hasWorkout;
        }

        return response()->json($progress);
    }
}
