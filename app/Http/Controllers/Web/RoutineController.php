<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ExerciseBase;
use App\Models\Routine;
use App\Models\RoutineLog;
use App\Models\WorkoutSetLog;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoutineController extends Controller
{
    public function index()
    {
        $routines       = Auth::user()->routines()->with('exercises')->get();
        $todayLogs      = RoutineLog::where('user_id', Auth::id())
            ->whereDate('completed_at', today())
            ->pluck('routine_id');
        $workoutHistory = RoutineLog::where('user_id', Auth::id())
            ->with('routine:id,name')
            ->orderBy('completed_at', 'desc')
            ->get();

        return view('routines.index', compact('routines', 'todayLogs', 'workoutHistory'));
    }

    public function create()
    {
        $exercises = ExerciseBase::orderBy('muscle_group')->orderBy('name')->get();
        $routine   = null;

        return view('routines.create', compact('exercises', 'routine'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'                       => 'required|string|max:255',
            'exercises'                  => 'required|array|min:1',
            'exercises.*.exercise_id'    => 'required|exists:exercises_base,id',
            'exercises.*.sets'           => 'required|integer|min:1',
            'exercises.*.reps'           => 'required|integer|min:1',
        ]);

        $routine  = Auth::user()->routines()->create(['name' => $request->name]);
        $syncData = [];

        foreach ($request->exercises as $index => $ex) {
            $syncData[$ex['exercise_id']] = [
                'sets' => $ex['sets'], 
                'reps' => $ex['reps'],
                'sort_order' => $index
            ];
        }

        $routine->exercises()->sync($syncData);

        return redirect()->route('routines.index')->with('success', 'Rutina creada correctamente.');
    }

    public function edit(Routine $routine)
    {
        abort_unless($routine->user_id === Auth::id(), 403);

        $routine->load('exercises');
        $exercises = ExerciseBase::orderBy('muscle_group')->orderBy('name')->get();

        return view('routines.create', compact('routine', 'exercises'));
    }

    public function update(Request $request, Routine $routine)
    {
        abort_unless($routine->user_id === Auth::id(), 403);

        $request->validate([
            'name'                       => 'required|string|max:255',
            'exercises'                  => 'required|array|min:1',
            'exercises.*.exercise_id'    => 'required|exists:exercises_base,id',
            'exercises.*.sets'           => 'required|integer|min:1',
            'exercises.*.reps'           => 'required|integer|min:1',
        ]);

        $routine->update(['name' => $request->name]);

        $syncData = [];
        foreach ($request->exercises as $index => $ex) {
            $syncData[$ex['exercise_id']] = [
                'sets' => $ex['sets'], 
                'reps' => $ex['reps'],
                'sort_order' => $index
            ];
        }
        $routine->exercises()->sync($syncData);

        return redirect()->route('routines.index')->with('success', 'Rutina actualizada.');
    }

    public function destroy(Routine $routine)
    {
        abort_unless($routine->user_id === Auth::id(), 403);
        $routine->delete();

        return back()->with('success', 'Rutina eliminada.');
    }

    public function train(Routine $routine)
    {
        abort_unless($routine->user_id === Auth::id(), 403);
        $routine->load('exercises');

        $exerciseIds = $routine->exercises->pluck('id');

        // Peso más reciente registrado por ejercicio (para mostrar como referencia)
        $lastWeights = WorkoutSetLog::where('user_id', Auth::id())
            ->whereIn('exercise_id', $exerciseIds)
            ->whereNotNull('weight_kg')
            ->where('weight_kg', '>', 0)
            ->orderBy('logged_at', 'desc')
            ->get()
            ->groupBy('exercise_id')
            ->map(fn($logs) => (float) $logs->first()->weight_kg)
            ->toArray();

        return view('routines.train', compact('routine', 'lastWeights'));
    }

    public function complete(Request $request, Routine $routine)
    {
        abort_unless($routine->user_id === Auth::id(), 403);

        $log = RoutineLog::create([
            'user_id'      => Auth::id(),
            'routine_id'   => $routine->id,
            'completed_at' => now(),
        ]);

        // Guardar logs de series con pesos
        if ($request->has('set_logs')) {
            foreach ($request->set_logs as $exIdx => $sets) {
                foreach ($sets as $setIdx => $set) {
                    if (!empty($set['done']) && !empty($set['exercise_id'])) {
                        WorkoutSetLog::create([
                            'user_id'        => Auth::id(),
                            'routine_log_id' => $log->id,
                            'exercise_id'    => $set['exercise_id'],
                            'set_number'     => $set['set_number'] ?? ($setIdx + 1),
                            'reps_done'      => $set['reps'] ?? 0,
                            'weight_kg'      => ($set['weight'] ?? 0) > 0 ? $set['weight'] : null,
                            'logged_at'      => now(),
                        ]);
                    }
                }
            }
        }

        return redirect()->route('routines.index')->with('success', '¡Entrenamiento completado y registrado!');
    }

    public function exercises()
    {
        return response()->json(
            ExerciseBase::orderBy('muscle_group')->orderBy('name')->get(['id', 'name', 'muscle_group'])
        );
    }

    public function importForm()
    {
        $exercises = ExerciseBase::orderBy('name')->get(['id', 'name', 'muscle_group']);
        return view('routines.import', compact('exercises'));
    }

    public function importStore(Request $request)
    {
        $request->validate([
            'name'             => 'required|string|max:255',
            'exercises'        => 'required|array|min:1',
            'exercises.*.name' => 'required|string|max:255',
            'exercises.*.sets' => 'required|integer|min:1|max:99',
            'exercises.*.reps' => 'required|integer|min:1|max:999',
        ]);

        $routine  = Auth::user()->routines()->create(['name' => $request->name]);
        $syncData = [];

        foreach ($request->exercises as $index => $ex) {
            $exercise = ExerciseBase::firstOrCreate(
                ['name' => trim($ex['name'])],
                ['muscle_group' => 'Otro']
            );
            // Si hay duplicado de ejercicio en el listado, sumamos sets
            if (isset($syncData[$exercise->id])) {
                $syncData[$exercise->id]['sets'] += (int) $ex['sets'];
            } else {
                $syncData[$exercise->id] = [
                    'sets'       => (int) $ex['sets'], 
                    'reps'       => (int) $ex['reps'],
                    'sort_order' => $index
                ];
            }
        }

        $routine->exercises()->sync($syncData);

        return redirect()->route('routines.index')->with('success', "Rutina «{$routine->name}» importada con " . count($syncData) . ' ejercicios.');
    }

    public function pdf()
    {
        $user     = Auth::user();
        $routines = $user->routines()->with('exercises')->get();

        $pdf = Pdf::loadView('routines.pdf', compact('routines', 'user'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('mis-rutinas-' . now()->format('Y-m-d') . '.pdf');
    }

    public function strength()
    {
        // Agrupar logs por ejercicio con progresión de pesos
        $logs = WorkoutSetLog::where('user_id', Auth::id())
            ->whereNotNull('weight_kg')
            ->where('weight_kg', '>', 0)
            ->with('exercise:id,name,muscle_group')
            ->orderBy('logged_at', 'asc')
            ->get();

        $exerciseProgress = $logs->groupBy('exercise_id')->map(function ($exLogs) {
            $exercise   = $exLogs->first()->exercise;
            $bySession  = $exLogs->groupBy(fn($l) => $l->logged_at->format('Y-m-d'));
            $dates      = $bySession->keys()->values();
            $maxWeights = $bySession->map(fn($sl) => $sl->max('weight_kg'))->values();
            $latest     = $maxWeights->last();
            $previous   = $maxWeights->count() > 1 ? $maxWeights->slice(-2, 1)->first() : null;

            return [
                'exercise'      => $exercise,
                'dates'         => $dates,
                'max_weights'   => $maxWeights,
                'latest_weight' => $latest,
                'prev_weight'   => $previous,
                'sessions'      => $bySession->count(),
                'trend'         => $previous !== null ? ($latest <=> $previous) : 0, // 1 up, 0 equal, -1 down
            ];
        })->values();

        return view('routines.strength', compact('exerciseProgress'));
    }
}
