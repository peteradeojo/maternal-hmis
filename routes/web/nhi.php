<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InsuranceController;

Route::name('nhi.')->prefix('nhis')->middleware(['role:billing', 'datalog'])->group(function () {
    Route::get('/patients', [InsuranceController::class, 'index'])->name('index');
    Route::get('/patients/{patient}', [InsuranceController::class, 'showPatient'])->name('show-patient');
    Route::match(['GET', 'POST'], '/patients/{profile}/edit', [InsuranceController::class, 'editProfile'])->name('edit-insurance');

    Route::get('/encounters', [InsuranceController::class, 'encounters'])->name('encounters');
    Route::get('/encounter/{visit}', [InsuranceController::class, 'showEncounter'])->name('show-encounter');

    Route::prefix('organizations')->name('orgs.')->group(function () {
        Route::get('/', [InsuranceController::class, 'getOrganizations'])->name('index');
        Route::post('/', [InsuranceController::class, 'createOrganization'])->name('create');
        Route::get('/{org}', [InsuranceController::class, 'showOrganization'])->name('show');
        Route::patch('/{org}', [InsuranceController::class, 'editOrganization'])->name('edit');
    });
});
