<?php

use App\Http\Controllers\Records\PatientsController;
use Illuminate\Support\Facades\Route;

Route::name('records.')->group(function () {
    Route::match(['get', 'post'], '/records/patients', [PatientsController::class, 'index'])->name('patients');
});
