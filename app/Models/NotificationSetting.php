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
    ];

    protected $casts = [
        'workout_reminder_enabled' => 'boolean',
        'water_reminder_enabled'   => 'boolean',
        'water_reminder_times'     => 'array',
        'water_goal_glasses'       => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
