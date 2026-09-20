<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\MeterController;
use App\Http\Controllers\PwaManifestController;
use App\Http\Controllers\ReadingController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/manifest.json', PwaManifestController::class)->name('pwa.manifest');
Route::get('/precache-manifest.json', \App\Http\Controllers\PrecacheManifestController::class)
    ->name('precache.manifest');

Route::get('/health', function () {
return response()->noContent();
});

Route::view('/offline', 'offline')->name('offline');
Route::get('/login', [LoginController::class,'show'])->name('login.show');
Route::post('/login', [LoginController::class,'authenticate'])->name('login');
Route::post('/logout', [LoginController::class,'logout'])->name('logout');

Route::middleware(['auth'])->group(function(){
    Route::resource('meters', MeterController::class);
    Route::post('meters/{meter}/readings', [ReadingController::class, 'store'])->name('meters.readings.store');
    Route::get('meters/{meter}/readings/{reading}/edit', [ReadingController::class, 'edit'])->name('meters.readings.edit');
    Route::put('meters/{meter}/readings/{reading}', [ReadingController::class, 'update'])->name('meters.readings.update');
    Route::delete('meters/{meter}/readings/{reading}', [ReadingController::class, 'destroy'])->name('meters.readings.destroy');
});
