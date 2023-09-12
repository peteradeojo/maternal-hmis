<?php

use App\Http\Controllers\Doctor\PatientsController;
use Illuminate\Support\Facades\Route;

Route::name('doctor.')->group(function () {
    Route::get('/patients', function () {
        return view('patients');
    })->name('patients');

    Route::match(['get', 'post'], '/treat/{visit}', [PatientsController::class, 'treat'])->name('treat');
    Route::match(['get', 'post'], '/review/{documentation}', [PatientsController::class, 'followUp'])->name('follow-up');

    Route::post('/treat/anc/{visit}', [PatientsController::class, 'treatAnc'])->name('treat-anc');

    Route::prefix('med')->group(function () {
        Route::get('/anc-bookings', [PatientsController::class, 'pendingAncBookings'])->name('anc-bookings');
        Route::post('/anc-bookings/{profile}', [PatientsController::class, 'submitAncBooking'])->name('submit-anc-booking');
    });
});
