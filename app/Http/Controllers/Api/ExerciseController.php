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
}
