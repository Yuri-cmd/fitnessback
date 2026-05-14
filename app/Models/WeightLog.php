<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeightLog extends Model
{
    protected $fillable = ['user_id', 'weight', 'logged_at'];

    protected $casts = [
        'logged_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
