<?php

use App\Enums\Department;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\Pharmacy\AdmissionsController;
use App\Http\Controllers\PharmacyController;
use Illuminate\Support\Facades\Route;

Route::prefix('phm')->name('phm.')->middleware(['role:pharmacy', 'datalog'])->group(function () {
    Route::get('/prescriptions', [PharmacyController::class, 'index'])->name('prescriptions');
    Route::get('/prescriptions/{doc}', [PharmacyController::class, 'show'])->name('get-prescription');
    Route::get('/get-prescriptions', [PharmacyController::class, 'getPrescriptions'])->name('get-prescriptions');
    Route::patch('/close-prescriptions/{doc}', [PharmacyController::class, 'closePrescription'])->name('close-prescription');

    Route::prefix('inventory')->name('inventory')->middleware(['role:pharmacy|admin'])->group(function () {
        Route::get('/', [InventoryController::class, 'index'])->name('.index');
        Route::post('/', [InventoryController::class, 'createStockItem']);

        Route::match(['GET', 'POST'], '/item/{item}', [InventoryController::class, 'viewStockDetails'])->name('.stock-details');

        Route::prefix('/purchases')->group(function () {
            Route::get('/', [InventoryController::class, 'purchaseOrders'])->name('.purchases');
            Route::get('/new', [InventoryController::class, 'createPurchaseOrder'])->name('.new-order');
            Route::post('/new', [InventoryController::class, 'storePurchaseOrder'])->name('.new-order');
            Route::get('/{order}', [InventoryController::class, 'viewOrder'])->name('.order');
            Route::post('/{order}', [InventoryController::class, 'editPurchaseOrder'])->name('.order');
        });

        Route::prefix('/suppliers')->group(function () {
            Route::match(['GET', 'POST'], '/', [InventoryController::class, 'suppliers'])->name('.suppliers');
        });

        Route::match(['GET', 'POST'], '/bulk-import', [InventoryController::class, 'bulkImport'])->name('.bulk-import');

        Route::prefix('/stock-take')->group(function () {
            Route::get('/', [InventoryController::class, 'stockTake'])->name('.stock-take');
            Route::get('/new', [InventoryController::class, 'newStockTake'])->name('.new-stock-take');
            Route::get('/{take}', [InventoryController::class, 'stockCount'])->name('.stock-count')->whereNumber(['take']);
        });
    });

    Route::prefix('admissions')->group(function () {
        Route::get('/', [PharmacyController::class, 'admissions'])->name('admissions');
        Route::get('/{admission}', [PharmacyController::class, 'showAdmissionTreatment'])->name('show-admission');
    });
});
