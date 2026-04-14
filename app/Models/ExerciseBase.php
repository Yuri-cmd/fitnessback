<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExerciseBase extends Model
{
    use HasFactory;

    protected $table = 'exercises_base';

    protected $fillable = [
        'name',
        'muscle_group',
        'equipment',
        'description',
        'instructions',
        'video_url',
        'is_public'
    ];

    protected $casts = [
        'instructions' => 'json',
    ];
}
