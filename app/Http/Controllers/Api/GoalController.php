<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Goal;
use App\Models\WeightLog;
use App\Models\RoutineLog;
use Illuminate\Http\Request;

class GoalController extends Controller
{
    public function index(Request $request)
    {
        $goals = $request->user()->goals()->get();

        // Actualizar progreso automáticamente
        foreach ($goals as $goal) {
            if ($goal->type === 'weight') {
                $lastWeight = WeightLog::where('user_id', $request->user()->id)->latest()->first();
                if ($lastWeight) {
                    $goal->current_value = $lastWeight->weight;
                    // Si la meta es bajar de peso y el peso actual es <= meta, o subir y es >= meta
                    // Necesitamos saber si el peso inicial era mayor o menor. 
                    // Por simplicidad para el prototipo, marcamos completado si alcanza el valor exacto o lo supera en la dirección deseada.
                }
            } elseif ($goal->type === 'workouts_weekly') {
                $startOfWeek = now()->startOfWeek();
                $count = RoutineLog::where('user_id', $request->user()->id)
                    ->where('completed_at', '>=', $startOfWeek)
                    ->count();
                $goal->current_value = $count;
            }
            
            if ($goal->current_value >= $goal->target_value && $goal->type !== 'weight') {
                $goal->is_completed = true;
            }
            $goal->save();
        }

        return $goals;
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|string',
            'target_value' => 'required|numeric',
            'deadline' => 'nullable|date',
        ]);

        $goal = $request->user()->goals()->create($request->all());

        return response()->json($goal, 201);
    }

    public function update(Request $request, Goal $goal)
    {
        if ($goal->user_id !== $request->user()->id) return response()->json(['message' => 'No autorizado'], 403);
        $goal->update($request->all());
        return $goal;
    }

    public function destroy(Request $request, Goal $goal)
    {
        if ($goal->user_id !== $request->user()->id) return response()->json(['message' => 'No autorizado'], 403);
        $goal->delete();
        return response()->json(['message' => 'Eliminado']);
    }
}
