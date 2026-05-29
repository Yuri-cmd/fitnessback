<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Routine extends Model
{
    protected $fillable = ['name', 'user_id', 'description', 'archived_at'];

    protected $casts = ['archived_at' => 'datetime'];

    public function exercises(): BelongsToMany
    {
        return $this->belongsToMany(ExerciseBase::class, 'routine_exercise', 'routine_id', 'exercise_id')
                    ->withPivot(['sets', 'reps', 'reps_max', 'sort_order', 'warmup_sets', 'warmup_reps', 'superset_group'])
                    ->orderBy('routine_exercise.sort_order');
    }
}
