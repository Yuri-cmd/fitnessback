<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    protected $fillable = [
        'user_id', 
        'height', 
        'current_weight', 
        'goal_weight', 
        'birth_date', 
        'gender', 
        'activity_level'
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];

    public function getAgeAttribute()
    {
        return $this->birth_date ? $this->birth_date->age : null;
    }

    /**
     * Harris-Benedict Equation
     */
    public function getBmrAttribute()
    {
        if (!$this->height || !$this->current_weight || !$this->age || !$this->gender) {
            return null;
        }

        if ($this->gender === 'male') {
            return 88.362 + (13.397 * $this->current_weight) + (4.799 * $this->height) - (5.677 * $this->age);
        } else {
            return 447.593 + (9.247 * $this->current_weight) + (3.098 * $this->height) - (4.330 * $this->age);
        }
    }

    public function getTdeeAttribute()
    {
        $bmr = $this->bmr;
        if (!$bmr) return null;

        $multipliers = [
            'sedentary' => 1.2,
            'lightly_active' => 1.375,
            'moderately_active' => 1.55,
            'very_active' => 1.725,
            'extra_active' => 1.9,
        ];

        return round($bmr * ($multipliers[$this->activity_level] ?? 1.2));
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
