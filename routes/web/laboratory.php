<?php

use App\Http\Controllers\LabController;
use Illuminate\Support\Facades\Route;

Route::prefix('/lab')->name('lab.')->group(function () {
    Route::get('/history', [LabController::class, 'history'])->name('history');
    Route::match(['get', 'post'], 'test/{documentation}', [LabController::class, 'test'])->name('take-test');
});
