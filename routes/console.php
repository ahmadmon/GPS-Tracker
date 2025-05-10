<?php


use App\Helpers\Hadis;
use Illuminate\Support\Facades\Schedule;

Schedule::call(new Hadis())->daily();
Schedule::command('send:expiry-notifications')->daily();
