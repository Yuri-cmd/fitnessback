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
            'description' => 'nullable|string',
        ]);

        $routine = $request->user()->routines()->create($request->only(['name', 'description']));

        return response()->json($routine);
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
}
