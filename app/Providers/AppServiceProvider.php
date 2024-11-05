<?php

namespace App\Providers;

use App\Http\Services\DeviceManager;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Auth::loginUsingId(1);

//        $data = '78781f122c0610060408cc03d40c1c05811f3024146c01b02315860114fe121224540d0a'; //concox Eslami - check ptN = 12
//        $data = '78782222180b030f1507c903d38ba405833b1823147101b0237ebe007aaa00020000144e0b0d0a'; //concox AT4 - check ptN = 22
//        $serial = '353549092222735';
////        dd(substr($data, 20, 2));
//        $deviceManager = new DeviceManager();
//        $deviceBrand =  $deviceManager->getDevice('concox');
//        dd($deviceBrand->parseData($data, $serial));


    }
}
