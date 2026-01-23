<?php

use App\Http\Controllers\AppointmentsController;
use App\Http\Controllers\Records\PatientsController;
use Illuminate\Support\Facades\Route;

Route::match(['get', 'post'], 'patients', [PatientsController::class, 'getPatients'])->name('patients');
Route::post('patients/{patient}/check-in', [PatientsController::class, 'checkIn'])->name('patient-check-in');
Route::post('check-out-patient/{visit}', [PatientsController::class, 'checkOut'])->name('check-out');
Route::post('/patents/insurance/{patient}', [PatientsController::class, 'addInsurancePRofile'])->name('insurance');

Route::get('/appointments', [AppointmentsController::class, 'fetchAppointments'])->name('appointments');
