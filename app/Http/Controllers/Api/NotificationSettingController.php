<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NotificationSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationSettingController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $setting = $request->user()->notificationSetting
            ?? NotificationSetting::firstOrCreate(
                ['user_id' => $request->user()->id],
                [
                    'workout_reminder_enabled' => true,
                    'workout_reminder_time'    => '20:00',
                    'water_reminder_enabled'   => true,
                    'water_reminder_times'     => ['09:00', '13:00', '18:00'],
                    'water_goal_glasses'       => 8,
                ]
            );

        return response()->json($setting);
    }

    public function update(Request $request): JsonResponse
    {
        $data = $request->validate([
            'workout_reminder_enabled' => 'sometimes|boolean',
            'workout_reminder_time'    => ['sometimes', 'string', 'regex:/^\d{2}:\d{2}$/'],
            'water_reminder_enabled'   => 'sometimes|boolean',
            'water_reminder_times'     => 'sometimes|array|min:1|max:6',
            'water_reminder_times.*'   => ['string', 'regex:/^\d{2}:\d{2}$/'],
            'water_goal_glasses'       => 'sometimes|integer|min:1|max:20',
        ]);

        $setting = $request->user()->notificationSetting
            ?? new NotificationSetting(['user_id' => $request->user()->id]);

        $setting->fill($data)->save();

        return response()->json($setting);
    }
}
