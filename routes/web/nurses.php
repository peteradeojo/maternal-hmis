<?php

use App\Http\Controllers\Controller;
use App\Http\Controllers\Nursing\PatientsController;
use App\Http\Controllers\VitalsController;
use Illuminate\Support\Facades\Route;

Route::name('nurses.')->group(function () {
    Route::get('/vitals', [VitalsController::class, 'index'])->name('vitals');
    Route::match(['post', 'get'], '/vitals/{visit}', [VitalsController::class, 'takeVitals'])->name('patient-vitals');
    Route::get('anc-bookings', [PatientsController::class, 'ancBookings'])->name('anc-bookings');

    Route::post('anc-bookings/{profile}', [PatientsController::class, 'submitAncBooking'])->name('submit-anc-booking');
});
