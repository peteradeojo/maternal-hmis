<?php

use App\Enums\Department;
use App\Http\Controllers\RadiologyController;
use Illuminate\Support\Facades\Route;

Route::prefix('rad')->name('rad.')->middleware(['datalog'])->group(function () {
    Route::middleware(['role:radiology'])->group(function () {
        Route::get('/scans', [RadiologyController::class, 'index'])->name('scans');
        Route::get('/history', [RadiologyController::class, 'history'])->name('history');
        Route::get('/scan/{scan}', [RadiologyController::class, 'show'])->name('scan');
        Route::post('/scan/{scan}', [RadiologyController::class, 'storeResult']);
    });

    Route::get('/results/{scan}', [RadiologyController::class, 'getScanResult'])->name('scan-results');
});
