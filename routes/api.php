<?php

use App\Events\ChatSent;
use App\Http\Controllers\IT\CrmController;
use App\Models\User;
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
Route::get('/health-check', function () {
    return response()->json(['status' => 'ok'], 200);
})->middleware('throttle:10,1');

Route::middleware(['auth', 'auth:sanctum', 'active_users'])->group(function () {
    Route::prefix('records')->name('api.records.')->group(base_path('routes/api/records.php'));

    Route::prefix('rad')->group(base_path('routes/api/rad.php'));

    include_once __DIR__ . '/api/laboratory.php';
    include_once __DIR__ . '/api/nursing.php';
    // include_once __DIR__ . '/api/rad.php';
    include_once __DIR__ . '/api/doctor.php';
    include_once __DIR__ . '/api/nhi.php';
    include_once __DIR__ . '/api/dispensary.php';
    include_once __DIR__ . '/api/billing.php';
    include_once __DIR__ . '/api/pharmacy.php';

    Route::post('/job-openings', [CrmController::class, 'createJobOpening']);
    Route::patch('/job-openings/{opening}', [CrmController::class, 'toggleJobOpening']);

    Route::get('/active-users', function (Request $request) {
        $users = User::active()
            ->with(['department'])
            ->where('id', '!=', $request->user()->id)
            ->select(['id', 'firstname', 'lastname', 'phone', 'department_id'])
            ->get();
        return response()->json($users);
    });

    Route::get('/chats/{fromId}', function (Request $request, $fromId) {});

    Route::post('/chat/send', function (Request $request) {
        $data = $request->validate([
            'message' => 'required|string',
            'to' => 'required|integer',
        ]);

        $data['time'] = now()->format('Y-m-d h:i A');
        $data['from'] = auth()->user()->id;
        $data['fromName'] = auth()->user()->name;

        event(new ChatSent($data));
        return response()->json(['ok' => true]);
    });
});
// include_once __DIR__ . '/api/records.php';

Route::get('/job-openings', [CrmController::class, 'getJobs']);
Route::get('/job-openings/{opening}', [CrmController::class, 'showJobOpening']);
