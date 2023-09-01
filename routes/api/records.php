<?php

use App\Http\Controllers\Records\PatientsController;
use Illuminate\Support\Facades\Route;

Route::prefix('records')->name('api.records.')->middleware(['auth:sanctum'])->group(function () {
    Route::get('patients', [PatientsController::class, 'getPatients'])->name('patients');
    Route::post('patients/check-in/{patient}', [PatientsController::class, 'checkIn'])->name('patients');
});
