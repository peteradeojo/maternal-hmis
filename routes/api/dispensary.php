<?php

use App\Http\Controllers\PharmacyController;
use Illuminate\Support\Facades\Route;

Route::name('dispensary.api.')->prefix('dispensary')->group(function () {
    Route::get('/prescriptions/data', [PharmacyController::class, 'getPrescriptions'])->name('prescriptions.data');
});
