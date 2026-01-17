<?php

use App\Http\Controllers\FinanceController;
use Illuminate\Support\Facades\Route;

Route::prefix('finance')
    // ->middleware(['role:admin|finance'])
    ->name('finance')->group(function () {
        Route::get('/', [FinanceController::class, 'index']);

        Route::name('.charts.')->group(function () {
            Route::get('/bill-trend', [FinanceController::class, 'getBillTrendStats'])->name('bill-trend');
        });
    });
