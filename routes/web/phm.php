<?php

use App\Http\Controllers\PharmacyController;
use Illuminate\Support\Facades\Route;

Route::prefix('phm')->name('phm.')->group(function () {
  Route::get('/prescriptions', [PharmacyController::class, 'index'])->name('prescriptions');
  Route::get('/get-prescriptions', [PharmacyController::class, 'getPrescriptions'])->name('get-prescriptions');
});
