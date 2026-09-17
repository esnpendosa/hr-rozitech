<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Check overdue tasks every hour
Schedule::command('tasks:check-overdue')->hourly();

// Check target risk every 6 hours
Schedule::command('targets:check-risk')->everySixHours();

// Check contract expiry daily at 8am
Schedule::command('employees:check-contract-expiry')->dailyAt('08:00');

// Clean expired reports daily at 2am
Schedule::command('reports:clean-expired')->dailyAt('02:00');
