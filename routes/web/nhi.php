<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InsuranceController;

Route::name('nhi.')->prefix('nhis')->group(function () {
    Route::get('/patients', [InsuranceController::class, 'index'])->name('index');
    Route::get('/patients/{patient}', [InsuranceController::class, 'showPatient'])->name('show-patient');
});
