<?php

use App\Http\Controllers\Doctor\PatientsController;
use Illuminate\Support\Facades\Route;

Route::prefix('doctor')->name('api.doctor.')->group(function () {
    Route::get('list-patients', [PatientsController::class, 'fetchPatients'])->name('fetch-patients');
    Route::get('visits', [PatientsController::class, 'getVisitsHistory'])->name('visits');
    // Route::get('visits', [PatientsController::class, 'getVisitsHistory'])->name('visits');
});
