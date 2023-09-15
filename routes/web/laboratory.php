<?php

use App\Http\Controllers\LabController;
use Illuminate\Support\Facades\Route;

Route::prefix('/lab')->name('lab.')->group(function () {
    Route::get('/history', [LabController::class, 'history'])->name('history');
    Route::get('/anc', function () {
        $user = auth()->user();
        return view('lab.ancs', compact('user'));
    })->name('antenatals');
    Route::match(['get', 'post'], 'test/{documentation}', [LabController::class, 'test'])->name('take-test');
    Route::match(['get'], 'report/{doc}', [LabController::class, 'testReport'])->name('report-test');
    Route::match(['get', 'post'], 'anc-test/{visit}', [LabController::class, 'testAnc'])->name('test-anc');
    // Route::get('/anc-booking/{profile}', [LabController::class, 'anc'])->name('anc-booking');
    Route::match(['get', 'post'], '/anc-booking/{profile}', [LabController::class, 'ancBooking'])->name('submit-anc-booking');
});
