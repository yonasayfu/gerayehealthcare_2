<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\ModuleController;
use App\Http\Controllers\Api\V1\PatientController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\API\LeaveRequestController;

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

Route::prefix('v1')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [UserController::class, 'me']);
        Route::patch('/user', [UserController::class, 'update']);
        Route::get('/users', [UserController::class, 'index']);
        Route::get('/modules', ModuleController::class);

        Route::get('/patients/me', [PatientController::class, 'me']);
        Route::patch('/patients/me', [PatientController::class, 'updateMe']);
        Route::apiResource('patients', PatientController::class)->except(['create', 'edit']);

        Route::get('/leave-requests/pending', [LeaveRequestController::class, 'getPendingRequests']);
    });
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Legacy route retained for backwards compatibility
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/leave-requests/pending', [LeaveRequestController::class, 'getPendingRequests']);
});
