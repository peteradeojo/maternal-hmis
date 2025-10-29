<?php

use App\Http\Controllers\Antenatal;
use App\Http\Controllers\Records\AdmissionsController;
use App\Http\Controllers\Records\HistoryController;
use App\Http\Controllers\Records\PatientsController;
use Illuminate\Support\Facades\Route;

Route::name('records.')->prefix('/records')->group(function () {
    Route::match(['get', 'post'], '/patients', [PatientsController::class, 'index'])->name('patients')->middleware(['datalog']);
    Route::match(['get', 'post'], '/patients/{patient}', [PatientsController::class, 'show'])->name('patient')->middleware(['datalog']);
    Route::match(['get', 'post'], '/patients/{patient}/edit', [PatientsController::class, 'edit'])->name('patient.edit')->middleware(['datalog']);
    Route::match(['get', 'post'], '/new', [PatientsController::class, 'create'])->name('patients.new')->middleware(['datalog']);
    Route::match(['get', 'post'], '/check-out/{visit}', [PatientsController::class, 'checkOut'])->name('force-check-out')->middleware(['datalog']);
    Route::match(['get', 'post'], '/patients/{patient}/anc-profile', [PatientsController::class, 'createAncProfile'])->name('patient.anc-profile')->middleware(['datalog']);
    Route::get("/fetch-history", [HistoryController::class, 'getHistory'])->name('get-history');
    Route::prefix('/visit-history')->group(function () {
        Route::get("/", [HistoryController::class, 'index'])->name('history');
        Route::get("/{visit}", [HistoryController::class, 'show'])->name('show-history');
    });

    // Admissions
    Route::prefix('admissions')->name('admissions')->group(function () {
        Route::get('/', [AdmissionsController::class, 'index']);
    });

    // Antenatal
    Route::get('/antenatal/new/{patient}', [Antenatal::class, 'create'])->name('new-anc');
    Route::post('/antenatal/new/{patient}', [Antenatal::class, 'create'])->middleware(['datalog'])->name('new-anc');

    Route::match(['GET', 'DELETE'], '/antenatal/{profile}/close', [Antenatal::class, 'closeProfile'])->middleware(['datalog'])->name('close-anc');

    // Visits
    Route::get('/create-visit/{patient}', [PatientsController::class, 'checkIn'])->name('start-visit');
});
