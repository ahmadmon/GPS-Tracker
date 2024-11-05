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

        $data = '78781f122c0610060408cc03d40c1c05811f3024146c01b02315860114fe121224540d0a';
        $serial = '202206181724169';
        $deviceManager = new DeviceManager();
        $deviceBrand =  $deviceManager->getDevice('concox');
        dd($deviceBrand->parseData($data, $serial));


    }
}
