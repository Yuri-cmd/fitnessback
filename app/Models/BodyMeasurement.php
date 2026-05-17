<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BodyMeasurement extends Model
{
    protected $fillable = [
        'user_id', 'waist_cm', 'chest_cm',
        'left_arm_cm', 'right_arm_cm',
        'left_leg_cm', 'right_leg_cm',
        'hips_cm', 'measured_at',
    ];

    protected $casts = ['measured_at' => 'date'];
}
