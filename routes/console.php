<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Alertes mail automatiques
Schedule::command('contrats:check-expiration')->dailyAt('08:00');
Schedule::command('payments:check-due')->dailyAt('08:15');
