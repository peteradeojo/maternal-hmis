<?php

use App\Http\Controllers\PharmacyController;
use Illuminate\Support\Facades\Route;

Route::name('dis.')->prefix('dispensary')->group(function (){
    Route::get('/prescriptions', [PharmacyController::class, 'dispensaryIndex'])->name('index');
    Route::match(['get', 'post'], '/entry', [PharmacyController::class, 'dispensaryShow'])->name('get-prescription');
    Route::get('/bill/{bill}', [PharmacyController::class, 'getBill'])->name('get-bill');
});
