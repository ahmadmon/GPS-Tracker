<?php

use App\Http\Controllers\DeviceController;
use App\Http\Controllers\VehicleController;
use Illuminate\Support\Facades\Route;



Route::middleware(['auth'])->group(function () {

    Route::get('/', function () {
        return view('admin');
    })->name('home');

    Route::resource('device', DeviceController::class);

    Route::resource('vehicle', VehicleController::class);

});

//Route::get('/send', function () {
//
//    $sms = new SmsService();
//
//    $sms->setTo('09337332513');
//    $sms->setText('سلام این تست است.');
//
//    $messageService = new MessageSerivce($sms);
//    $messageService->send();
//});

require __DIR__.'/auth.php';
