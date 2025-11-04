<?php

use App\Http\Controllers\BillingController;
use Illuminate\Support\Facades\Route;

Route::prefix('billing')->name('billing.')->group(function () {
    Route::get('/', [BillingController::class, 'index'])->name('index');
    Route::get('/patient/{patient}', [BillingController::class, 'patientBills'])->name('patient-bills');

    Route::get('/get-bill/{visit}', [BillingController::class, 'getVisitBill'])->name('get-bill');
    Route::get('/view-bills/{visit}', [BillingController::class, 'listPatientBills'])->name('view-bills');

    Route::get('/payment-form/{bill}', [BillingController::class, 'getPaymentForm'])->name('init-payment');
    Route::delete('/cancel-bill/{bill}', [BillingController::class, 'deleteBill'])->name('cancel-bill');
});
