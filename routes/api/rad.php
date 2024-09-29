<?php

use App\Http\Controllers\RadiologyController;
use Illuminate\Support\Facades\Route;

Route::name('api.rad.')->group(function () {
    Route::get('/scans', [RadiologyController::class, 'getScans'])->name('scans.data');
    Route::get('/history', [RadiologyController::class, 'getScansHistory'])->name('scans-history');
    Route::get('/scan-result/{scan}', [RadiologyController::class, 'scanResult'])->name('scans.result');
});
