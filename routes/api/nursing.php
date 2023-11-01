<?php

use App\Http\Controllers\AdmissionsController;
use App\Http\Controllers\Nursing\PatientsController;
use Illuminate\Support\Facades\Route;

Route::prefix('nursing')->name('api.nursing.')->group(function () {
    Route::get('/anc-bookings', [PatientsController::class, 'getAncBookings'])->name('anc-bookings');
    Route::get('/admissions', [AdmissionsController::class, 'getAdmissions'])->name('admissions');
});
