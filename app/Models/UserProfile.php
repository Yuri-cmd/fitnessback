<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    protected $fillable = ['user_id', 'height', 'current_weight', 'goal_weight'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
