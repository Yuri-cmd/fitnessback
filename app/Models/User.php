<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's profile.
     */
    public function profile()
    {
        return $this->hasOne(UserProfile::class);
    }

    /**
     * Get the user's weight logs.
     */
    public function weightLogs()
    {
        return $this->hasMany(WeightLog::class);
    }

    /**
     * Get the user's routines.
     */
    public function routines()
    {
        return $this->hasMany(Routine::class);
    }

    public function goals()
    {
        return $this->hasMany(Goal::class);
    }

    public function waterLogs()
    {
        return $this->hasMany(WaterLog::class);
    }

    public function achievements()
    {
        return $this->belongsToMany(Achievement::class)->withPivot('earned_at')->withTimestamps();
    }

    public function fcmTokens()
    {
        return $this->hasMany(FcmToken::class);
    }

    public function routineLogs()
    {
        return $this->hasMany(RoutineLog::class);
    }

    public function streak()
    {
        return $this->hasOne(UserStreak::class);
    }

    public function notificationSetting()
    {
        return $this->hasOne(NotificationSetting::class);
    }
}
