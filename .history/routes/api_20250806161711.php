<?php

use App\Http\Controllers\Api\V1\AuthController;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\V1\PatientController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DateConversionController;

Route::prefix('v1')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);

        Route::get('/user', function (Request $request) {
            return $request->user();
        });

        Route::apiResource('patients', PatientController::class)->only(['index', 'show']);
    });
    
    // Date conversion routes
    Route::post('/convert-to-ethiopian', [DateConversionController::class, 'convertToEthiopian']);
    // Removed convert-to-gregorian as it will be handled on frontend
});
