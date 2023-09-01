<?php

use App\Http\Controllers\Records\PatientsController;
use Illuminate\Support\Facades\Route;

Route::name('records.')->prefix('/records')->group(function () {
    Route::match(['get', 'post'], '/patients', [PatientsController::class, 'index'])->name('patients');
    Route::match(['get', 'post'], '/patients/{patient}', [PatientsController::class, 'show'])->name('patient');
    Route::match(['get', 'post'], '/new', [PatientsController::class, 'create'])->name('patients.new');
});
