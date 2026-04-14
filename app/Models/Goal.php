<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Goal extends Model
{
    protected $fillable = ['user_id', 'type', 'target_value', 'current_value', 'deadline', 'is_completed'];

    protected $casts = [
        'is_completed' => 'boolean',
        'deadline' => 'date',
        'target_value' => 'float',
        'current_value' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
