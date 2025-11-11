<?php

use App\Http\Controllers\LabController;
use Illuminate\Support\Facades\Route;

Route::name('api.lab.')->prefix('laboratory')->group(function () {
    Route::get('/tests', [LabController::class, 'getTests'])->name('tests');
    Route::get('/history', [LabController::class, 'getHistory'])->name('history');
    Route::get('/anc-patients', [LabController::class, 'getAncVisits'])->name('antenatal-tests');

    Route::post('/save-test/{test}', [LabController::class, 'saveTest'])->name('save-test');
});
