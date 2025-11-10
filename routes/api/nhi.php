<?php

use App\Http\Controllers\InsuranceController;
use App\Http\Controllers\Records\PatientsController;
use Illuminate\Support\Facades\Route;

Route::prefix('api')->middleware(['auth:sanctum'])->name('api.nhi.')->group(function () {
    Route::get('/patients', [InsuranceController::class, 'getPatients'])->name('patients');
    Route::get('/visits', [PatientsController::class, 'getVisits'])->name('visits');
});
