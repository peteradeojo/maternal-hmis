<?php

use App\Http\Controllers\Records\PatientsController;
use Illuminate\Support\Facades\Route;

Route::name('records.')->prefix('/records')->group(function () {
    Route::match(['get', 'post'], '/patients', [PatientsController::class, 'index'])->name('patients')->middleware(['datalog']);
    Route::match(['get', 'post'], '/patients/{patient}', [PatientsController::class, 'show'])->name('patient')->middleware(['datalog']);
    Route::match(['get', 'post'], '/new', [PatientsController::class, 'create'])->name('patients.new')->middleware(['datalog']);
    Route::match(['get', 'post'], '/check-out/{visit}', [PatientsController::class, 'checkOut'])->name('force-check-out')->middleware(['datalog']);
    Route::match(['get', 'post'], '/patients/{patient}/anc-profile', [PatientsController::class, 'createAncProfile'])->name('patient.anc-profile')->middleware(['datalog']);
});
