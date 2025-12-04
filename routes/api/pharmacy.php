<?php

use App\Http\Controllers\InventoryController;
use App\Http\Controllers\PharmacyController;
use Illuminate\Support\Facades\Route;

Route::name('phm-api')->middleware(['api', 'auth'])->group(function () {
    Route::prefix('inventory')->group(function () {
        Route::get('/', [InventoryController::class, 'getInventory'])->name('.get-inventory');
    });

    Route::get('/reverse-lookup', [PharmacyController::class, 'reverseLookup'])->name('.reverse-lookup');
});
