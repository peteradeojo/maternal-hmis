<?php

use App\Http\Controllers\FinanceController;
use Illuminate\Support\Facades\Route;

Route::prefix('finance')->name('finance')->group(function () {
    Route::get('/', [FinanceController::class, 'index']);
});
