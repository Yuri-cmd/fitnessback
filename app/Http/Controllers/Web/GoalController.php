<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Goal;
use App\Models\RoutineLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GoalController extends Controller
{
    public function index()
    {
        $weeklyWorkouts = RoutineLog::where('user_id', Auth::id())
            ->whereBetween('completed_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->count();

        $latestWeight = Auth::user()->weightLogs()->latest()->value('weight');

        $goals = Auth::user()->goals()->get()->map(function ($goal) use ($weeklyWorkouts, $latestWeight) {
            if ($goal->type === 'workouts_weekly') {
                $goal->current_value = $weeklyWorkouts;
            } elseif ($goal->type === 'weight' && $latestWeight) {
                $goal->current_value = $latestWeight;
            }

            return $goal;
        });

        return view('goals.index', compact('goals'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type'         => 'required|in:weight,workouts_weekly',
            'target_value' => 'required|numeric|min:1',
            'deadline'     => 'nullable|date|after:today',
        ]);

        Auth::user()->goals()->create($request->only('type', 'target_value', 'deadline'));

        return back()->with('success', 'Meta creada correctamente.');
    }

    public function destroy(Goal $goal)
    {
        abort_unless($goal->user_id === Auth::id(), 403);
        $goal->delete();

        return back()->with('success', 'Meta eliminada.');
    }
}
