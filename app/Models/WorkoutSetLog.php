<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkoutSetLog extends Model
{
    protected $fillable = [
        'user_id',
        'routine_log_id',
        'exercise_id',
        'set_number',
        'reps_done',
        'weight_kg',
        'logged_at',
    ];

    protected $casts = [
        'logged_at' => 'datetime',
        'weight_kg' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function routineLog()
    {
        return $this->belongsTo(RoutineLog::class);
    }

    public function exercise()
    {
        return $this->belongsTo(ExerciseBase::class, 'exercise_id');
    }
}
