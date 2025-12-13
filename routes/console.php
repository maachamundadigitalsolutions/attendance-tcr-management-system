<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// Default inspire command
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ✅ Attendance reminder scheduler
Schedule::command('attendance:reminders')
    ->everyThirtyMinutes()
    ->between('9:00', '23:00'); // run checks every 30 min

