<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WeightLog;
use Illuminate\Http\Request;

class WeightController extends Controller
{
    public function index(Request $request)
    {
        return $request->user()->weightLogs()->orderBy('created_at', 'desc')->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'weight' => 'required|numeric|min:20|max:300',
        ]);

        $log = $request->user()->weightLogs()->create([
            'weight' => $request->weight,
        ]);

        // Actualizar el peso actual en el perfil tambin
        $profile = $request->user()->profile;
        if ($profile) {
            $profile->current_weight = $request->weight;
            $profile->save();
        }

        return response()->json($log);
    }
}
