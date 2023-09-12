<?php

use App\Http\Controllers\Nursing\PatientsController;
use Illuminate\Support\Facades\Route;

Route::prefix('nursing')->name('api.nursing.')->group(function () {
    Route::get('/anc-bookings', [PatientsController::class, 'getAncBookings'])->name('anc-bookings');
});
