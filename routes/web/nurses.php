<?php

use Illuminate\Support\Facades\Route;

Route::name('nurses.')->group(function () {
    Route::get('/vitals', function () {
    })->name('vitals');
    Route::match(['post', 'get'], '/vitals/{patient}', function () {
    })->name('patient-vitals');
});
