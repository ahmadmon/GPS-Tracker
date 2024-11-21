<?php

use App\Http\Controllers\Admin\GeofenceController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VehicleController;
use App\Livewire\MapPage;
use Illuminate\Support\Facades\Route;


Route::middleware(['auth'])->group(function () {

    Route::get('/', function () {
        return view('admin');
    })->name('home');

    Route::get('/map', MapPage::class)->name('map');

    Route::resource('device', DeviceController::class);
    Route::get('/device/device-setting/{device}', [DeviceController::class, 'deviceSetting'])->name('device.device-setting');
    Route::post('/device/store-sms/{device}', [DeviceController::class, 'storeSMS'])->name('device.store-sms');
    Route::get('/device/get-location/{id}', [DeviceController::class, 'location'])->name('device.get-location');

    Route::resource('vehicle', VehicleController::class);
    Route::resource('user', UserController::class);
    Route::resource('company', CompanyController::class);
    Route::delete('company/remove-subsets/{company}/{id}', [CompanyController::class, 'removeSubsets'])->name('company.remove-subsets');
    Route::get('company/manage-subsets/{company}', [CompanyController::class, 'manageSubsets'])->name('company.manage-subsets');
    Route::resource('geofence', GeofenceController::class);

    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'index'])->name('index');
        Route::post('/change-password', [ProfileController::class, 'changePassword'])->name('change-password');
        Route::get('/forgot-password', [ProfileController::class, 'forgotPassword'])->name('forgot-password');
    });

});


require __DIR__ . '/auth.php';
