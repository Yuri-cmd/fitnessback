<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WaterLogController extends Controller
{
    public function index(Request $request)
    {
        $totalMl = $request->user()->waterLogs()
            ->whereDate('logged_at', now()->toDateString())
            ->sum('amount_ml');

        return response()->json([
            'total_ml' => (int) $totalMl,
            'date'     => now()->toDateString(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'amount_ml' => 'required|integer|min:50|max:1000',
        ]);

        $log = $request->user()->waterLogs()->create([
            'amount_ml' => $request->amount_ml,
            'logged_at' => now(),
        ]);

        return response()->json($log, 201);
    }
}
