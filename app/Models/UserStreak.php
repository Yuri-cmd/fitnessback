<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserStreak extends Model
{
    protected $fillable = [
        'user_id',
        'workout_streak',
        'water_streak',
        'best_workout_streak',
        'best_water_streak',
        'last_workout_date',
        'last_water_date',
    ];

    protected $casts = [
        'last_workout_date' => 'date',
        'last_water_date'   => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
