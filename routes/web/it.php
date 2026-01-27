<?php

use App\Enums\Department;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IT\StaffController;
use App\Http\Controllers\AdmissionsController;
use App\Http\Controllers\IT\CrmController;
use App\Http\Controllers\IT\ProductsController;
use App\Http\Middleware\VerifyCsrfToken;
use Symfony\Component\HttpFoundation\StreamedResponse;

Route::name('it.')->group(function () {
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/department/{dep}', [StaffController::class, 'department'])->name('department');

        Route::match(['get', 'post'], '/wards', [AdmissionsController::class, 'wards'])->name('wards');

        Route::match(['get', 'post'], '/staff', [StaffController::class, 'index'])->name('staff');
        Route::match(['get', 'post'], '/staff/{user}', [StaffController::class, 'show'])->name('staff.view');
        Route::patch('/staff/{user}/status', [StaffController::class, 'changeUserStatus'])->name('staff.update-status');
        Route::get('/info', function () {
            return response(phpinfo(), 200)
                ->header('Content-Type', 'text/html');
        });

        Route::prefix('products')->group(function () {
            Route::get('/', [ProductsController::class, 'index'])->name('products');
            Route::post('/', [ProductsController::class, 'addProducts']);
            Route::get('/get-products', [ProductsController::class, 'fetchProducts'])->middleware(['api', 'auth'])->name('get-products');
            Route::match(['GET', 'POST'], '/get-products/{product}', [ProductsController::class, 'show'])->name('show-product');
        });
        Route::view('/logs', 'it.logs');
    });

    Route::prefix('/crm')->middleware(['role:media|admin'])->group(function () {
        Route::get('/', [CrmController::class, 'index'])->name('crm-index');
        Route::get('/publish', [CrmController::class, 'create'])->name('crm-publish');
        Route::post('/publish', [CrmController::class, 'publish']);
        Route::get('/{post}', [CrmController::class, 'show'])->name('crm-show');
        Route::put('/{post}/status', [CrmController::class, 'updatePostStatus'])->name('crm-status');
    });

});
