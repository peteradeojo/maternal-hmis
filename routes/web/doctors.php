<?php

use App\Http\Controllers\Doctor\PatientsController;
use Illuminate\Support\Facades\Route;

Route::name('doctor.')->group(function () {
    Route::get('/patients', function () {
        return view('patients');
    })->name('patients');

    Route::match(['get', 'post'], '/treat/{visit}', [PatientsController::class, 'treat'])->name('treat');

    Route::post('/treat/anc/{visit}', [PatientsController::class, 'treatAnc'])->name('treat-anc');
});
