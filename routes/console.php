<?php

use App\Jobs\SendWorkoutReminderJob;
use App\Jobs\SendWaterReminderJob;
use App\Jobs\UpdateStreaksJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Cada minuto revisa qué usuarios tienen recordatorio configurado para ahora
Schedule::job(new SendWorkoutReminderJob)->everyMinute()->name('workout-reminders');
Schedule::job(new SendWaterReminderJob)->everyMinute()->name('water-reminders');

// Cada día a medianoche actualiza/reinicia las rachas
Schedule::job(new UpdateStreaksJob)->dailyAt('00:05')->name('update-streaks');
