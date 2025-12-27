<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InsuranceController;

Route::name('nhi.')->prefix('nhis')->middleware(['role:billing', 'datalog'])->group(function () {
    Route::get('/patients', [InsuranceController::class, 'index'])->name('index');
    Route::get('/patients/{patient}', [InsuranceController::class, 'showPatient'])->name('show-patient');

    Route::get('/encounters', [InsuranceController::class, 'encounters'])->name('encounters');
    Route::get('/encounter/{visit}', [InsuranceController::class, 'showEncounter'])->name('show-encounter');
});
