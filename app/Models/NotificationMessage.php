<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationMessage extends Model
{
    protected $fillable = ['notification_type_id', 'body', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function notificationType()
    {
        return $this->belongsTo(NotificationType::class);
    }

    public static function random(string $type, ?string $context = null): ?self
    {
        return self::whereHas('notificationType', function ($q) use ($type, $context) {
            $q->where('type', $type)->where('context', $context);
        })->where('is_active', true)->inRandomOrder()->first();
    }
}
