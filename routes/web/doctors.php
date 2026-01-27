<?php

use App\Enums\Department;
use App\Http\Controllers\AdmissionsController;
use App\Http\Controllers\Doctor\PatientsController;
use App\Http\Middleware\RestrictDepartment;
use Illuminate\Support\Facades\Route;

Route::name('doctor.')->middleware(['datalog', 'auth'])->group(function () {
    Route::middleware(['role:doctor'])->group(function () {
        Route::match(['get', 'post'], '/treat/{visit}', [PatientsController::class, 'treat'])->name('treat');
        Route::match(['get', 'post'], '/review/{documentation}', [PatientsController::class, 'followUp'])->name('follow-up');

        Route::post('/treat/anc/{visit}', [PatientsController::class, 'treatAnc'])->name('treat-anc');
        Route::middleware(['auth', 'api'])->post('/examination/{visit}', [PatientsController::class, 'addExamination'])->name('examine');

        Route::get('/anclog/{visit}', [PatientsController::class, 'getAncLog'])->name('anc-log');

        Route::prefix('med')->group(function () {
            Route::post('/notes/{visit}', [PatientsController::class, 'note'])->whereNumber('visit')->name('visit-note');

            Route::get('/patients', [PatientsController::class, 'index'])->name('patients');

            Route::get('/patients/{patient}', [PatientsController::class, 'show'])->name('patient');

            Route::get('/anc-bookings', [PatientsController::class, 'pendingAncBookings'])->name('anc-bookings');

            Route::match(['get', 'post'], '/anc-bookings/{profile}', [PatientsController::class, 'submitAncBooking'])->name('submit-anc-booking');

            Route::prefix('admissions')->group(function () {
                Route::match(['get', 'post'], '/start/{visit}', [PatientsController::class, 'startAdmission'])->name('start-admission');
            });
        });

        Route::prefix('antenatal')->group(function () {
            Route::get('/profile/{patient}', [PatientsController::class, 'viewAncProfile'])->name('anc-profile');
        });

        Route::prefix('admissions')->group(function () {
            Route::get('/', [AdmissionsController::class, 'index'])->name('admissions');
            Route::post('/create/{visit}', [AdmissionsController::class, 'createAdmission'])->name('admit');
            Route::get('/{admission}', [AdmissionsController::class, 'show'])->name('show-admission');
            Route::get('/{admission}/edit', [AdmissionsController::class, 'edit'])->name('show-admission-plan');
            Route::get('/{admission}/review', [AdmissionsController::class, 'reviewNote'])->name('admissions.review');
        });

        Route::put('/notes/{note}', [AdmissionsController::class, 'updateNote'])->name('admissions.update-note');
    });

    Route::get('/visits', [PatientsController::class, 'history'])->name('history');
    Route::get('/visits/{visit}', [PatientsController::class, 'visit'])->name('visit');
});

Route::name('doctor.')->middleware(['auth', 'datalog'])->group(function () {
    Route::get('/operation-note/{opnote}', [AdmissionsController::class, 'getOpNote'])->name('admission.op-note');
});
