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
        return $request->user()->routines()->with('exercises')->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'exercises' => 'required|array',
            'exercises.*.exercise_id' => 'required|exists:exercises_base,id',
            'exercises.*.sets' => 'required|integer',
            'exercises.*.reps' => 'required|integer',
        ]);

        $routine = $request->user()->routines()->create([
            'name' => $request->name,
        ]);

        foreach ($request->exercises as $ex) {
            $routine->exercises()->attach($ex['exercise_id'], [
                'sets' => $ex['sets'],
                'reps' => $ex['reps']
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
            'user_id' => $request->user()->id,
            'routine_id' => $routine->id,
            'completed_at' => now(),
        ]);

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
        ]);

        $routine->update(['name' => $request->name]);

        // Sincronizamos los ejercicios (borra los viejos y pone los nuevos en el pivot)
        $syncData = [];
        foreach ($request->exercises as $ex) {
            $syncData[$ex['exercise_id']] = [
                'sets' => $ex['sets'],
                'reps' => $ex['reps']
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
        // Laravel startOfWeek es Lunes (1), Domingo (7) o (0) dependiendo de config
        // Vamos a mapear de 1 a 7
        for ($i = 1; $i <= 7; $i++) {
            $hasWorkout = $logs->contains(function ($log) use ($i) {
                return $log->completed_at->dayOfWeekIso == $i;
            });
            $progress[$i] = $hasWorkout;
        }

        return response()->json($progress);
    }
}
