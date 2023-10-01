<?php

use App\Http\Controllers\RadiologyController;
use Illuminate\Support\Facades\Route;

Route::name('api.rad.')->group(function () {
    Route::get('/scans', [RadiologyController::class, 'getScans'])->name('scans.data');
});
