<?php

use App\Http\Controllers\Records\PatientsController;
use Illuminate\Support\Facades\Route;

Route::prefix('records')->name('api.records.')->group(function () {
    Route::get('patients', [PatientsController::class, 'getPatients'])->middleware(['auth:sanctum'])->name('patients');
});
