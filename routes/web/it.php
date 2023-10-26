<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IT\StaffController;
use App\Http\Controllers\AdmissionsController;

Route::name('it.')->group(function () {
    Route::get('/department/{dep}', [StaffController::class, 'department'])->name('department');

    Route::match(['get', 'post'], '/wards', [AdmissionsController::class, 'wards'])->name('wards');

    Route::match(['get', 'post'], '/staff', [StaffController::class, 'index'])->name('staff');
    Route::match(['get', 'post'], '/staff/{user}', [StaffController::class, 'show'])->name('staff.view');
    Route::get('/info', function () {
        return response(phpinfo(), 200)
            ->header('Content-Type', 'text/html');
    });
});
