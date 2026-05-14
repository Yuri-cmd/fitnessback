<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WeightController extends Controller
{
    public function index()
    {
        $user       = Auth::user();
        $profile    = $user->profile;
        $weightLogs = $user->weightLogs()->orderBy('created_at', 'desc')->get();

        [$bmi, $bmiCategory, $weightToLose] = $this->calcBmi($profile);

        $chartData = $weightLogs->sortBy('created_at')->map(fn ($log) => [
            'date'   => $log->created_at->format('d/m'),
            'weight' => (float) $log->weight,
        ])->values();

        return view('weight.index', compact('profile', 'weightLogs', 'bmi', 'bmiCategory', 'weightToLose', 'chartData'));
    }

    public function store(Request $request)
    {
        $request->validate(['weight' => 'required|numeric|min:20|max:300']);

        $user = Auth::user();
        $user->weightLogs()->create(['weight' => $request->weight]);

        $profile = $user->profile;
        if ($profile) {
            $profile->update(['current_weight' => $request->weight]);
        }

        return back()->with('success', 'Peso registrado correctamente.');
    }

    private function calcBmi($profile): array
    {
        if (! $profile || ! $profile->height || ! $profile->current_weight) {
            return [null, 'Sin datos', 0];
        }

        $hm  = $profile->height / 100;
        $bmi = round($profile->current_weight / ($hm * $hm), 1);

        $category = match (true) {
            $bmi < 18.5 => 'Bajo peso',
            $bmi < 25   => 'Normal',
            $bmi < 30   => 'Sobrepeso',
            default     => 'Obesidad',
        };

        $idealWeight  = 24.9 * ($hm * $hm);
        $weightToLose = max(0, round($profile->current_weight - $idealWeight, 1));

        return [$bmi, $category, $weightToLose];
    }
}
