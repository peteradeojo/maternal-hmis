<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/login', function () {
    return view('login');
});
Route::get('logout', function (Request $request) {
    auth()->logout();
    $request->session()->invalidate();
    return redirect()->route('login');
})->name('logout');
Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::middleware(['auth'])->group(function () {
    Route::get('/', function () {
        return redirect()->route('dashboard');
    });

    Route::get('/radiology/{path}',function (Request $request, $path) {
        return response()->file(storage_path('app/radiology/' . $path));
    });

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [DashboardController::class, 'profile'])->name('user-profile');
    Route::post('/profile', [DashboardController::class, 'changePassword']);

    include __DIR__ . '/web/doctors.php';
    include __DIR__ . '/web/nurses.php';
    include __DIR__ . '/web/records.php';
    include __DIR__ . '/web/it.php';
    include __DIR__ . '/web/laboratory.php';
    include __DIR__ . '/web/rad.php';
    include __DIR__ . '/web/phm.php';
    include __DIR__ . '/web/dis.php';
    include __DIR__ . '/web/nhi.php';
});
