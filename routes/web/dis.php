<?php

use App\Http\Controllers\PharmacyController;
use Illuminate\Support\Facades\Route;

Route::name('dis.')->prefix('dispensary')->group(function (){
    Route::get('/prescriptions', [PharmacyController::class, 'index'])->name('index');
    Route::match(['get', 'post'], '/prescriptions/{doc}', [PharmacyController::class, 'dispensaryShow'])->name('get-prescription');
});
