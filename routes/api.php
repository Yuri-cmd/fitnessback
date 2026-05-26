<?php

use App\Http\Controllers\Api\AchievementController;
use App\Http\Controllers\Api\BodyMeasurementController;
use App\Http\Controllers\Api\VersionController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ExerciseController;
use App\Http\Controllers\Api\FcmTokenController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\NotificationSettingController;
use App\Http\Controllers\Api\GoalController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\RoutineController;
use App\Http\Controllers\Api\StatsController;
use App\Http\Controllers\Api\StreakController;
use App\Http\Controllers\Api\WaterLogController;
use App\Http\Controllers\Api\WeightController;
use Illuminate\Support\Facades\Route;

Route::get('/version', [VersionController::class, 'check']);

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::put('/password', [AuthController::class, 'changePassword']);
    Route::delete('/account', [AuthController::class, 'deleteAccount']);
    
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::post('/profile', [ProfileController::class, 'update']);
    
    Route::get('/weight-logs', [WeightController::class, 'index']);
    Route::post('/weight-logs', [WeightController::class, 'store']);
    
    Route::get('/exercises', [ExerciseController::class, 'index']);
    Route::post('/exercises', [ExerciseController::class, 'store']);
    Route::put('/exercises/{exercise}', [ExerciseController::class, 'update']);

    Route::get('/routines', [RoutineController::class, 'index']);
    Route::get('/routines/archived', [RoutineController::class, 'archived']);
    Route::post('/routines', [RoutineController::class, 'store']);
    Route::put('/routines/{routine}', [RoutineController::class, 'update']);
    Route::delete('/routines/{routine}', [RoutineController::class, 'destroy']);
    Route::post('/routines/{routine}/complete', [RoutineController::class, 'complete']);
    Route::post('/routines/{routine}/archive', [RoutineController::class, 'archive']);
    Route::post('/routines/{routine}/unarchive', [RoutineController::class, 'unarchive']);
    Route::get('/workouts/history', [RoutineController::class, 'workoutHistory']);
    Route::get('/workouts/weekly-progress', [RoutineController::class, 'weeklyProgress']);

    Route::get('/goals', [GoalController::class, 'index']);
    Route::post('/goals', [GoalController::class, 'store']);
    Route::put('/goals/{goal}', [GoalController::class, 'update']);
    Route::delete('/goals/{goal}', [GoalController::class, 'destroy']);

    // Hidratación
    Route::get('/water-logs', [WaterLogController::class, 'index']);
    Route::post('/water-logs', [WaterLogController::class, 'store']);
    Route::delete('/water-logs/last', [WaterLogController::class, 'destroy']);

    // Rachas
    Route::get('/streaks', [StreakController::class, 'index']);

    // Configuración de notificaciones
    Route::get('/notification-settings', [NotificationSettingController::class, 'show']);
    Route::put('/notification-settings', [NotificationSettingController::class, 'update']);

    // Logros
    Route::get('/achievements', [AchievementController::class, 'index']);

    // Medidas corporales
    Route::get('/measurements', [BodyMeasurementController::class, 'index']);
    Route::post('/measurements', [BodyMeasurementController::class, 'store']);
    Route::delete('/measurements/{bodyMeasurement}', [BodyMeasurementController::class, 'destroy']);

    // FCM Tokens
    Route::post('/fcm-token', [FcmTokenController::class, 'store']);
    Route::delete('/fcm-token', [FcmTokenController::class, 'destroy']);

    // Notificaciones push (prueba)
    Route::prefix('notifications')->group(function () {
        Route::post('/test',         [NotificationController::class, 'test']);
        Route::post('/broadcast',    [NotificationController::class, 'broadcast']);
        Route::post('/send-to-user', [NotificationController::class, 'sendToUser']);
    });

    // Estadísticas
    Route::prefix('stats')->group(function () {
        Route::get('/weight-history', [StatsController::class, 'weightHistory']);
        Route::get('/volume-by-muscle', [StatsController::class, 'volumeByMuscle']);
        Route::get('/activity-heatmap', [StatsController::class, 'activityHeatmap']);
        Route::get('/personal-records', [StatsController::class, 'personalRecords']);
    });
});
