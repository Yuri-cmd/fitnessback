<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ExerciseBase;
use Illuminate\Http\Request;

class WikiController extends Controller
{
    public function index(Request $request)
    {
        $query = ExerciseBase::query();

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->has('muscle')) {
            $query->where('muscle_group', $request->muscle);
        }

        $exercises = $query->orderBy('name')->paginate(20);
        $muscles = ExerciseBase::select('muscle_group')->distinct()->pluck('muscle_group');

        return view('wiki.index', compact('exercises', 'muscles'));
    }

    public function show(ExerciseBase $exercise)
    {
        return view('wiki.show', compact('exercise'));
    }
}
