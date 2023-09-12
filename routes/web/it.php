<?php

use App\Http\Controllers\IT\StaffController;
use Illuminate\Support\Facades\Route;

Route::name('it.')->group(function () {
    Route::match(['get', 'post'], '/staff', [StaffController::class, 'index'])->name('staff');
    Route::get('/info', function () {
        return response(phpinfo(), 200)
            ->header('Content-Type', 'text/html');
    });
});
