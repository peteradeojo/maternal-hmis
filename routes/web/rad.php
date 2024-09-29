<?php

use App\Http\Controllers\RadiologyController;
use Illuminate\Support\Facades\Route;

Route::prefix('rad')->name('rad.')->group(function () {
    Route::get('/scans', [RadiologyController::class, 'index'])->name('scans');
    Route::get('/scan/{doc}', [RadiologyController::class, 'show'])->name('scan');
    Route::post('/scan/{scan}', [RadiologyController::class, 'storeResult'])->name('scan');
});
