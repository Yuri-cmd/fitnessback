<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserStreak;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StreakController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $streak = $request->user()->streak
            ?? UserStreak::firstOrCreate(['user_id' => $request->user()->id]);

        return response()->json($streak);
    }
}
