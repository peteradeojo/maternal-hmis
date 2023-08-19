<?php

use Illuminate\Support\Facades\Route;

Route::name('doctor.')->group(function () {
    Route::get('/patients', function () {
        return view('patients');
    })->name('patients');

    Route::get('/treat/{patient}', [PatientController::class, 'treat'])->name('treat');
});
