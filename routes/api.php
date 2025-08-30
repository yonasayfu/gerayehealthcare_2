<?php

use App\Http\Controllers\Api\V1\AuthController;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\V1\PatientController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\VisitServiceController as ApiVisitServiceController;
use App\Http\Controllers\Api\V1\CaregiverAssignmentController as ApiCaregiverAssignmentController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DateConversionController;
use App\Http\Controllers\Api\V1\ServiceController;
use App\Http\Controllers\Api\V1\MessageController as ApiMessageController;
use App\Http\Controllers\Api\V1\NotificationController as ApiNotificationController;
use App\Http\Controllers\Api\V1\PushTokenController;
use App\Http\Controllers\Api\V1\DocumentController as ApiDocumentController;
use App\Http\Controllers\Api\V1\BillingController as ApiBillingController;

Route::prefix('v1')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);

        Route::get('/user', function (Request $request) {
            return $request->user();
        });

        // Profile endpoints
        Route::get('/me', [UserController::class, 'me']);
        Route::patch('/me', [UserController::class, 'update']);

        Route::apiResource('patients', PatientController::class)->only(['index', 'show']);

        // Patient self endpoints
        Route::get('/patients/me', [PatientController::class, 'me']);
        Route::patch('/patients/me', [PatientController::class, 'updateMe']);

        // Visit services for mobile staff usage
        Route::get('/visit-services/my-schedule', [ApiVisitServiceController::class, 'mySchedule'])->middleware('throttle:60,1');
        Route::post('/visit-services', [ApiVisitServiceController::class, 'store'])->middleware('throttle:30,1');
        Route::patch('/visit-services/{visitService}', [ApiVisitServiceController::class, 'update'])->middleware('throttle:30,1');
        Route::delete('/visit-services/{visitService}', [ApiVisitServiceController::class, 'destroy'])->middleware('throttle:30,1');
        Route::post('/visit-services/{visitService}/check-in', [ApiVisitServiceController::class, 'checkIn'])->middleware('throttle:20,1');
        Route::post('/visit-services/{visitService}/check-out', [ApiVisitServiceController::class, 'checkOut'])->middleware('throttle:20,1');

        // Caregiver assignments context
        Route::get('/caregiver-assignments/my-active', [ApiCaregiverAssignmentController::class, 'myActive']);
        Route::get('/caregiver-assignments/my-patients', [ApiCaregiverAssignmentController::class, 'myPatients']);

        // Services catalog
        Route::get('/services', [ServiceController::class, 'index']);

        // Messaging
        Route::get('/messages/threads', [ApiMessageController::class, 'threads'])->middleware('throttle:60,1');
        Route::get('/messages/threads/{user}', [ApiMessageController::class, 'thread'])->middleware('throttle:60,1');
        Route::post('/messages/threads/{user}/messages', [ApiMessageController::class, 'send'])->middleware('throttle:30,1');

        // Notifications
        Route::get('/notifications', [ApiNotificationController::class, 'index']);
        Route::post('/notifications/{notification}/read', [ApiNotificationController::class, 'markRead']);

        // Push tokens
        Route::post('/push-tokens', [PushTokenController::class, 'store']);
        Route::delete('/push-tokens', [PushTokenController::class, 'destroy']);

        // Documents
        Route::get('/documents/my', [ApiDocumentController::class, 'my']);
        Route::get('/documents/{document}/download', [ApiDocumentController::class, 'download']);
        Route::post('/documents', [ApiDocumentController::class, 'store']);

        // Invoices & Insurance (read-only)
        Route::get('/invoices/my', [ApiBillingController::class, 'myInvoices']);
        Route::get('/invoices/{invoice}', [ApiBillingController::class, 'showInvoice']);
        Route::get('/insurance/policies/my', [ApiBillingController::class, 'myPolicies']);
        Route::get('/insurance/claims/my', [ApiBillingController::class, 'myClaims']);
        Route::get('/insurance/claims/{claim}', [ApiBillingController::class, 'showClaim']);
    });
    
    // Date conversion routes
    Route::post('/convert-to-ethiopian', [DateConversionController::class, 'convertToEthiopian']);
    Route::post('/convert-gregorian-to-ethiopian', [DateConversionController::class, 'convertGregorianToEthiopian']);
    Route::post('/convert-ethiopian-to-gregorian', [DateConversionController::class, 'convertEthiopianToGregorian']);
});
