<?php

use App\Enums\Department;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\PharmacyController;
use Illuminate\Support\Facades\Route;

Route::prefix('phm')->name('phm.')->middleware(['datalog'])->group(function () {
    Route::get('/prescriptions', [PharmacyController::class, 'index'])->name('prescriptions');
    Route::get('/prescriptions/{doc}', [PharmacyController::class, 'show'])->name('get-prescription');
    Route::get('/get-prescriptions', [PharmacyController::class, 'getPrescriptions'])->name('get-prescriptions');
    Route::patch('/close-prescriptions/{doc}', [PharmacyController::class, 'closePrescription'])->name('close-prescription');

    Route::prefix('inventory')->name('inventory')->middleware(['department:' . join(",", [Department::PHA->value, Department::IT->value])])->group(function () {
        Route::get('/', [InventoryController::class, 'index'])->name('.index');
    });
});
