<?php

use App\Http\Controllers\AdmissionsController;
use App\Http\Controllers\AppointmentsController;
use App\Http\Controllers\Doctor\PatientsController;
use App\Http\Controllers\LabController;
use App\Http\Controllers\RadiologyController;
use App\Http\Controllers\Records\PatientsController as RecordsPatientsController;
use App\Http\Controllers\VisitsController;
use Illuminate\Support\Facades\Route;

Route::prefix('doctor')->name('api.doctor.')->middleware(['datalog'])->group(function () {
    Route::get('list-patients', [PatientsController::class, 'fetchPatients'])->name('fetch-patients');
    Route::get('visits', [PatientsController::class, 'getVisitsHistory'])->name('visits');

    Route::get('anc-bookings', [RecordsPatientsController::class, 'getAntenatalAppointments'])->name('anc-appointments');

    Route::post('/note/{visit}', [PatientsController::class, 'note'])->name('note');
    Route::post('/diagnosis/{visit}', [PatientsController::class, 'saveDiagnosis'])->name('diagnosis');
    Route::post('/store-test/{visit}', [LabController::class, 'store'])->name('add-test');
    Route::post('/store-scan/{visit}', [RadiologyController::class, 'store'])->name('add-scan');

    Route::post('/admission/{admission}/operation-note', [AdmissionsController::class, 'saveOperationNote'])->name('save-op-note');
    Route::post('/admission/{admission}/delivery-note', [AdmissionsController::class, 'saveDeliveryNote'])->name('save-delivery-note');

    Route::post('/admission/{admission}/discharge', [AdmissionsController::class, 'setForDischarge'])->name('discharge');

    Route::get('/consultations', [VisitsController::class, 'index'])->name('consultations');

    Route::post('/appointments', [AppointmentsController::class, 'store'])->name('save-appointment');
});
