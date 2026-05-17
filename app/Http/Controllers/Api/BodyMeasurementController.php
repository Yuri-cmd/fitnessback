<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BodyMeasurement;
use Illuminate\Http\Request;

class BodyMeasurementController extends Controller
{
    public function index(Request $request)
    {
        return $request->user()
            ->hasMany(BodyMeasurement::class, 'user_id')
            ->orderBy('measured_at', 'desc')
            ->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'waist_cm'     => 'nullable|numeric|min:0',
            'chest_cm'     => 'nullable|numeric|min:0',
            'left_arm_cm'  => 'nullable|numeric|min:0',
            'right_arm_cm' => 'nullable|numeric|min:0',
            'left_leg_cm'  => 'nullable|numeric|min:0',
            'right_leg_cm' => 'nullable|numeric|min:0',
            'hips_cm'      => 'nullable|numeric|min:0',
            'measured_at'  => 'required|date',
        ]);

        $m = BodyMeasurement::create(array_merge($data, [
            'user_id' => $request->user()->id,
        ]));

        return response()->json($m, 201);
    }

    public function destroy(Request $request, BodyMeasurement $bodyMeasurement)
    {
        if ($bodyMeasurement->user_id !== $request->user()->id) {
            return response()->json(['message' => 'No autorizado'], 403);
        }
        $bodyMeasurement->delete();
        return response()->json(null, 204);
    }
}
