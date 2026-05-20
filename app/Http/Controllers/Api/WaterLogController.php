<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserStreak;
use Illuminate\Http\Request;

class WaterLogController extends Controller
{
    private const ML_PER_GLASS = 250;

    public function index(Request $request)
    {
        $user        = $request->user();
        $today       = now()->toDateString();
        $goalGlasses = $user->notificationSetting?->water_goal_glasses ?? 8;
        $totalMl     = (int) $user->waterLogs()->whereDate('logged_at', $today)->sum('amount_ml');
        $glasses     = (int) floor($totalMl / self::ML_PER_GLASS);

        return response()->json([
            'date'          => $today,
            'total_ml'      => $totalMl,
            'glasses'       => $glasses,
            'goal_glasses'  => $goalGlasses,
            'goal_reached'  => $glasses >= $goalGlasses,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'glasses' => 'required|integer|min:1|max:10',
        ]);

        $amountMl = $request->glasses * self::ML_PER_GLASS;

        $log = $request->user()->waterLogs()->create([
            'amount_ml' => $amountMl,
            'logged_at' => now(),
        ]);

        // Recalcular totales para devolver estado actualizado
        $user        = $request->user();
        $today       = now()->toDateString();
        $goalGlasses = $user->notificationSetting?->water_goal_glasses ?? 8;
        $totalMl     = (int) $user->waterLogs()->whereDate('logged_at', $today)->sum('amount_ml');
        $glasses     = (int) floor($totalMl / self::ML_PER_GLASS);

        // Si acaba de alcanzar la meta, actualizar racha de agua
        if ($glasses >= $goalGlasses) {
            $streak = $user->streak ?? UserStreak::firstOrCreate(['user_id' => $user->id]);
            if ($streak->last_water_date?->toDateString() !== $today) {
                $newStreak = $streak->water_streak + 1;
                $streak->water_streak    = $newStreak;
                $streak->last_water_date = $today;
                if ($newStreak > $streak->best_water_streak) {
                    $streak->best_water_streak = $newStreak;
                }
                $streak->save();
            }
        }

        return response()->json([
            'date'         => $today,
            'total_ml'     => $totalMl,
            'glasses'      => $glasses,
            'goal_glasses' => $goalGlasses,
            'goal_reached' => $glasses >= $goalGlasses,
        ], 201);
    }

    public function destroy(Request $request)
    {
        $user  = $request->user();
        $today = now()->toDateString();

        // Elimina el último vaso registrado hoy
        $last = $user->waterLogs()
            ->whereDate('logged_at', $today)
            ->latest('logged_at')
            ->first();

        if (!$last) {
            return response()->json(['message' => 'No hay registros hoy'], 404);
        }

        $last->delete();

        $goalGlasses = $user->notificationSetting?->water_goal_glasses ?? 8;
        $totalMl     = (int) $user->waterLogs()->whereDate('logged_at', $today)->sum('amount_ml');
        $glasses     = (int) floor($totalMl / self::ML_PER_GLASS);

        return response()->json([
            'date'         => $today,
            'total_ml'     => $totalMl,
            'glasses'      => $glasses,
            'goal_glasses' => $goalGlasses,
            'goal_reached' => $glasses >= $goalGlasses,
        ]);
    }
}
