<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoutineLog extends Model
{
    protected $fillable = ['user_id', 'routine_id', 'completed_at'];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    public function routine()
    {
        return $this->belongsTo(Routine::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
