<?php

use App\Http\Controllers\IT\CrmController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
// });

Route::middleware(['auth'])->group(function () {
    Route::prefix('records')->name('api.records.')->group(base_path('routes/api/records.php'));

    Route::prefix('rad')->group(base_path('routes/api/rad.php'));

    include_once __DIR__ . '/api/laboratory.php';
    include_once __DIR__ . '/api/nursing.php';
    // include_once __DIR__ . '/api/rad.php';
    include_once __DIR__ . '/api/doctor.php';
    include_once __DIR__ . '/api/nhi.php';
    include_once __DIR__ . '/api/dispensary.php';
});
// include_once __DIR__ . '/api/records.php';

Route::get('/posts', [CrmController::class, 'index']);
Route::get('/posts/{post}', [CrmController::class, 'show']);
