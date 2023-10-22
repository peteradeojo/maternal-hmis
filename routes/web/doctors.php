<?php

use App\Http\Controllers\Doctor\PatientsController;
use Illuminate\Support\Facades\Route;

Route::name('doctor.')->group(function () {

    Route::match(['get', 'post'], '/treat/{visit}', [PatientsController::class, 'treat'])->name('treat');
    Route::match(['get', 'post'], '/review/{documentation}', [PatientsController::class, 'followUp'])->name('follow-up');

    Route::post('/treat/anc/{visit}', [PatientsController::class, 'treatAnc'])->name('treat-anc');

    Route::prefix('med')->group(function () {
        Route::get('/patients', [PatientsController::class, 'index'])->name('patients');
        Route::get('/patients/{patient}', [PatientsController::class, 'show'])->name('patient');
        Route::get('/anc-bookings', [PatientsController::class, 'pendingAncBookings'])->name('anc-bookings');
        Route::match(['get', 'post'], '/anc-bookings/{profile}', [PatientsController::class, 'submitAncBooking'])->name('submit-anc-booking');
    });
});
