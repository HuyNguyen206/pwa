<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\MeterController;
use App\Http\Controllers\ReadingController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', [LoginController::class,'show'])->name('login.show');
Route::post('/login', [LoginController::class,'authenticate'])->name('login');
Route::post('/logout', [LoginController::class,'logout'])->name('logout');

Route::middleware(['auth'])->group(function(){
    Route::get('/', fn()=>redirect()->route('meters.index'));
    Route::resource('meters', MeterController::class);
    Route::post('meters/{meter}/readings', [ReadingController::class, 'store'])->name('meters.readings.store');
    Route::get('meters/{meter}/readings/{reading}/edit', [ReadingController::class, 'edit'])->name('meters.readings.edit');
    Route::put('meters/{meter}/readings/{reading}', [ReadingController::class, 'update'])->name('meters.readings.update');
    Route::delete('meters/{meter}/readings/{reading}', [ReadingController::class, 'destroy'])->name('meters.readings.destroy');
});