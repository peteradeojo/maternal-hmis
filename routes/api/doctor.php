<?php

use App\Http\Controllers\Doctor\PatientsController;
use App\Http\Controllers\LabController;
use App\Http\Controllers\RadiologyController;
use Illuminate\Support\Facades\Route;

Route::prefix('doctor')->name('api.doctor.')->group(function () {
    Route::get('list-patients', [PatientsController::class, 'fetchPatients'])->name('fetch-patients');
    Route::get('visits', [PatientsController::class, 'getVisitsHistory'])->name('visits');

    Route::middleware(['api', 'auth:sanctum'])->group(function () {
        Route::post('/note/{visit}', [PatientsController::class, 'note'])->name('note');
        Route::post('/diagnosis/{visit}', [PatientsController::class, 'saveDiagnosis'])->name('diagnosis');
        Route::post('/store-test/{visit}', [LabController::class, 'store'])->name('add-test');
        Route::post('/store-scan/{visit}', [RadiologyController::class, 'store'])->name('add-scan');
    });
});
