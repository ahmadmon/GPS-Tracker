<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Helpers\Hadis;

Schedule::call(new Hadis())->daily();
