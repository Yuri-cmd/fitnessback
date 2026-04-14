<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Routine extends Model
{
    protected $fillable = ['name', 'user_id', 'description'];

    public function exercises(): BelongsToMany
    {
        return $this->belongsToMany(ExerciseBase::class, 'routine_exercise', 'routine_id', 'exercise_id')
                    ->withPivot(['sets', 'reps']);
    }
}
