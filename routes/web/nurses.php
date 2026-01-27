<?php

use App\Enums\Department;
use App\Http\Controllers\AdmissionsController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Nursing\PatientsController;
use App\Http\Controllers\VitalsController;
use Illuminate\Support\Facades\Route;

Route::name('nurses.')->middleware(['role:nurse', 'datalog'])->group(function () {
    Route::get('/vitals', [VitalsController::class, 'index'])->name('vitals');
    Route::match(['post', 'get'], '/vitals/{visit}', [VitalsController::class, 'takeVitals'])->name('patient-vitals')->middleware(['datalog']);
    Route::get('anc-bookings', [PatientsController::class, 'ancBookings'])->name('anc-bookings');

    Route::get('anc-bookings/{profile}', [PatientsController::class, 'viewAncBooking'])->name('view-anc-booking');
    Route::post('anc-bookings/{profile}', [PatientsController::class, 'submitAncBooking'])->name('submit-anc-booking')->middleware(['datalog']);

    Route::prefix('/nurses/admissions')->name('admissions.')->group(function () {
        Route::get('/', [AdmissionsController::class, 'index'])->name('get');
        Route::match(['get', 'post'], '/{admission}/view', [AdmissionsController::class, 'show'])->name('show');
        Route::match(['get', 'post'], '/{admission}/assign-ward', [AdmissionsController::class, 'assignWard'])->name('assign-ward');
        Route::match(['get', 'post'], '/{admission}/preview-treatment', [AdmissionsController::class, 'previewTreatment'])->name('treatment-preview');

        Route::post('/discharge/{admission}', [AdmissionsController::class, 'discharge'])->name('discharge');

        Route::post('/consent-form/{admission}', [AdmissionsController::class, 'saveConsent'])->name('consent-form');
    });
});
