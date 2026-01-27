<?php

use App\Http\Controllers\BillingController;
use Illuminate\Support\Facades\Route;

Route::prefix('billing')->name('billing.')->group(function () {
    Route::get('/billable-patients', [BillingController::class, 'getPendingBills'])->name('get-billable-patients');
});
