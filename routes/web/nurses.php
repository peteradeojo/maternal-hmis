<?php

use App\Http\Controllers\VitalsController;
use Illuminate\Support\Facades\Route;

Route::name('nurses.')->group(function () {
    Route::get('/vitals', [VitalsController::class, 'index'])->name('vitals');
    Route::match(['post', 'get'], '/vitals/{visit}', [VitalsController::class, 'takeVitals'])->name('patient-vitals');
});
