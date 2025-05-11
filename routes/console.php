<?php


use App\Helpers\Hadis;
use Illuminate\Support\Facades\Schedule;

// Daily Hadis Api
Schedule::call(new Hadis())->daily();

// Subscription Expiry Notification
Schedule::command('send:expiry-notifications')
    ->dailyAt('12:00')
    ->runInBackground();

// Subscription Auto Renew
Schedule::command('subscriptions:auto-renew')
    ->everySixHours()
    ->runInBackground();
