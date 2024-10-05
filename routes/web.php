<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/', function () {
        return view('admin');
    })->name('home');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/send', function () {

    try {

        $sms = Melipayamak\Laravel\Facade::sms();
        $to = '09337332513';
        $from = '50004001854432';
        $text = 'تست وب سرویس ملی پیامک';
        $response = $sms->send($to, $from, $text);
    $json = json_decode($response);
        echo $json->Value; //RecId or Error Number
    } catch (Exception $e) {
        echo $e->getMessage();
    }
});

require __DIR__.'/auth.php';
