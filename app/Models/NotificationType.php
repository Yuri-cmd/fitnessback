<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationType extends Model
{
    protected $fillable = ['type', 'context', 'label'];

    public function messages()
    {
        return $this->hasMany(NotificationMessage::class);
    }
}
