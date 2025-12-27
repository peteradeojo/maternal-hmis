<?php

use App\Http\Controllers\IAMController;
use Illuminate\Support\Facades\Route;

Route::prefix('iam')->name('iam.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/', [IAMController::class, 'index'])->name('index');
    Route::get('/roles', [IAMController::class, 'roles'])->name('roles');
    Route::get('/permissions', [IAMController::class, 'permissions'])->name('permissions');
    Route::get('/audit-logs', [IAMController::class, 'auditLogs'])->name('audit-logs');
    Route::get('/datalogs', [IAMController::class, 'datalogs'])->name('datalogs');

    Route::get('/get-audit-logs', [IAMController::class, 'getAuditLogs'])->name('get-audit-logs');
    Route::get('/get-datalogs', [IAMController::class, 'getDatalogs'])->name('get-datalogs');
});
