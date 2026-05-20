<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationSetting extends Model
{
    protected $fillable = [
        'user_id',
        'workout_reminder_enabled',
        'workout_reminder_time',
        'water_reminder_enabled',
        'water_reminder_times',
        'water_goal_glasses',
        'morning_motivation_enabled',
        'morning_motivation_time',
        'evening_motivation_enabled',
        'evening_motivation_time',
        'birthday_notification_enabled',
    ];

    protected $casts = [
        'workout_reminder_enabled'     => 'boolean',
        'water_reminder_enabled'       => 'boolean',
        'water_reminder_times'         => 'array',
        'water_goal_glasses'           => 'integer',
        'morning_motivation_enabled'   => 'boolean',
        'evening_motivation_enabled'   => 'boolean',
        'birthday_notification_enabled' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
