<?php

use App\Http\Controllers\DeviceController;
use App\Http\Services\Notify\MessageSerivce;
use App\Http\Services\Notify\SMS\SmsService;
use Illuminate\Support\Facades\Route;


//Route::get('/dashboard', function () {
//    return view('dashboard');
//})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth'])->group(function () {

    Route::get('/', function () {
        return view('admin');
    })->name('home');

    Route::resource('device', DeviceController::class);
    Route::post('/device/connect-device/{device}', [DeviceController::class, 'connectToDevice'])->name('device.connect-to-device');

//    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

//Route::get('/send', function () {
//
//    $sms = new SmsService();
//
//    $sms->setTo('09337332513');
//    $sms->setText('سلام این تست است.');
////    $res = $sms->fire();
//
//    $messageService = new MessageSerivce($sms);
//    $res = $messageService->send();
//    dd($res);
//});

require __DIR__.'/auth.php';
