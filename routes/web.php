<?php

use App\Http\Controllers\Admin\GeofenceController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VehicleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/', function (Request $request) {
    Log::info('Received tracker data: ', $request->all());
});

Route::middleware(['auth'])->group(function () {

    Route::get('/', function () {
        return view('admin');
    })->name('home');

    Route::resource('device', DeviceController::class);
    Route::get('/device/device-setting/{device}', [DeviceController::class, 'deviceSetting'])->name('device.device-setting');
    Route::post('/device/store-sms/{device}', [DeviceController::class, 'storeSMS'])->name('device.store-sms');
    Route::get('/device/get-location/{id}', [DeviceController::class, 'location'])->name('device.get-location');

    Route::resource('vehicle', VehicleController::class);
    Route::resource('user', UserController::class);
    Route::resource('company', CompanyController::class);
    Route::resource('geofence', GeofenceController::class);

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
