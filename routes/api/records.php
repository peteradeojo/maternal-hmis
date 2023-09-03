<?php

use App\Http\Controllers\Records\PatientsController;
use Illuminate\Support\Facades\Route;

Route::match(['get', 'post'], 'patients', [PatientsController::class, 'getPatients'])->name('patients');
Route::post('patients/{patient}/check-in', [PatientsController::class, 'checkIn'])->name('patient-check-in');
