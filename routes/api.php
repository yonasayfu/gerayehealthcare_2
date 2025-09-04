<?php

use App\Http\Controllers\Api\DateConversionController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\BillingController as ApiBillingController;
use App\Http\Controllers\Api\V1\CaregiverAssignmentController as ApiCaregiverAssignmentController;
use App\Http\Controllers\Api\V1\DocumentController as ApiDocumentController;
use App\Http\Controllers\Api\V1\MessageController as ApiMessageController;
use App\Http\Controllers\Api\V1\NotificationController as ApiNotificationController;
use App\Http\Controllers\Api\V1\PatientController;
use App\Http\Controllers\Api\V1\PushTokenController;
use App\Http\Controllers\Api\V1\ServiceController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\VisitServiceController as ApiVisitServiceController;
use App\Http\Controllers\GroupMessageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

        Route::get('/users', [UserController::class, 'index'])->name('users.index');

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

        // Group Messaging (API)
        Route::post('/groups', [GroupMessageController::class, 'createGroup'])->name('api.groups.create');
        Route::get('/groups', [GroupMessageController::class, 'getGroups'])->name('api.groups.list');
        Route::get('/groups/{group}/messages', [GroupMessageController::class, 'index'])->name('api.groups.messages.index');
        Route::post('/groups/{group}/messages', [GroupMessageController::class, 'store'])->name('api.groups.messages.store');
        Route::patch('/groups/{group}/messages/{message}', [GroupMessageController::class, 'update'])->name('api.groups.messages.update');
        Route::delete('/groups/{group}/messages/{message}', [GroupMessageController::class, 'destroy'])->name('api.groups.messages.destroy');
        Route::post('/groups/{group}/messages/{message}/react', [GroupMessageController::class, 'react'])->name('api.groups.messages.react');

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

        // Marketing APIs
        Route::prefix('marketing')->group(function () {
            Route::get('/campaigns', [App\Http\Controllers\Api\V1\MarketingController::class, 'campaigns']);
            Route::post('/campaigns', [App\Http\Controllers\Api\V1\MarketingController::class, 'createCampaign']);
            Route::put('/campaigns/{campaign}', [App\Http\Controllers\Api\V1\MarketingController::class, 'updateCampaign']);
            Route::delete('/campaigns/{campaign}', [App\Http\Controllers\Api\V1\MarketingController::class, 'deleteCampaign']);
            Route::get('/campaigns/{campaign}/leads', [App\Http\Controllers\Api\V1\MarketingController::class, 'campaignLeads']);

            Route::get('/leads', [App\Http\Controllers\Api\V1\MarketingController::class, 'leads']);
            Route::post('/leads', [App\Http\Controllers\Api\V1\MarketingController::class, 'createLead']);
            Route::post('/leads/{lead}/convert', [App\Http\Controllers\Api\V1\MarketingController::class, 'convertLead']);

            Route::get('/analytics', [App\Http\Controllers\Api\V1\MarketingController::class, 'analytics']);
        });

        // Inventory APIs
        Route::prefix('inventory')->group(function () {
            Route::get('/items', [App\Http\Controllers\Api\V1\InventoryController::class, 'items']);
            Route::post('/items', [App\Http\Controllers\Api\V1\InventoryController::class, 'createItem']);
            Route::put('/items/{item}', [App\Http\Controllers\Api\V1\InventoryController::class, 'updateItem']);
            Route::delete('/items/{item}', [App\Http\Controllers\Api\V1\InventoryController::class, 'deleteItem']);
            Route::post('/items/{item}/adjust-stock', [App\Http\Controllers\Api\V1\InventoryController::class, 'adjustStock']);

            Route::get('/requests', [App\Http\Controllers\Api\V1\InventoryController::class, 'requests']);
            Route::post('/requests', [App\Http\Controllers\Api\V1\InventoryController::class, 'createRequest']);

            Route::get('/analytics', [App\Http\Controllers\Api\V1\InventoryController::class, 'analytics']);
        });

        // Insurance APIs
        Route::prefix('insurance')->group(function () {
            Route::get('/companies', [App\Http\Controllers\Api\V1\InsuranceController::class, 'companies']);
            Route::post('/companies', [App\Http\Controllers\Api\V1\InsuranceController::class, 'createCompany']);

            Route::get('/policies', [App\Http\Controllers\Api\V1\InsuranceController::class, 'policies']);
            Route::post('/policies', [App\Http\Controllers\Api\V1\InsuranceController::class, 'createPolicy']);

            Route::get('/claims', [App\Http\Controllers\Api\V1\InsuranceController::class, 'claims']);
            Route::post('/claims', [App\Http\Controllers\Api\V1\InsuranceController::class, 'createClaim']);
            Route::put('/claims/{claim}/status', [App\Http\Controllers\Api\V1\InsuranceController::class, 'updateClaimStatus']);

            Route::get('/analytics', [App\Http\Controllers\Api\V1\InsuranceController::class, 'analytics']);
        });

        // Analytics APIs
        Route::prefix('analytics')->group(function () {
            Route::get('/dashboard', [App\Http\Controllers\Api\V1\AnalyticsController::class, 'dashboard']);
            Route::get('/patients', [App\Http\Controllers\Api\V1\AnalyticsController::class, 'patients']);
            Route::get('/visits', [App\Http\Controllers\Api\V1\AnalyticsController::class, 'visits']);
            Route::get('/revenue', [App\Http\Controllers\Api\V1\AnalyticsController::class, 'revenue']);
            Route::get('/staff', [App\Http\Controllers\Api\V1\AnalyticsController::class, 'staff']);
        });

        // Bulk Operations APIs
        Route::prefix('bulk')->group(function () {
            Route::post('/patients', [App\Http\Controllers\Api\V1\BulkOperationsController::class, 'createPatients']);
            Route::put('/patients', [App\Http\Controllers\Api\V1\BulkOperationsController::class, 'updatePatients']);
            Route::post('/staff', [App\Http\Controllers\Api\V1\BulkOperationsController::class, 'createStaff']);
            Route::post('/inventory-items', [App\Http\Controllers\Api\V1\BulkOperationsController::class, 'createInventoryItems']);
            Route::delete('/delete', [App\Http\Controllers\Api\V1\BulkOperationsController::class, 'bulkDelete']);
            Route::post('/export', [App\Http\Controllers\Api\V1\BulkOperationsController::class, 'bulkExport']);
            Route::get('/operations/{operationId}/status', [App\Http\Controllers\Api\V1\BulkOperationsController::class, 'getOperationStatus']);
        });
    });

    // Date conversion routes
    Route::post('/convert-to-ethiopian', [DateConversionController::class, 'convertToEthiopian']);
    Route::post('/convert-gregorian-to-ethiopian', [DateConversionController::class, 'convertGregorianToEthiopian']);
    Route::post('/convert-ethiopian-to-gregorian', [DateConversionController::class, 'convertEthiopianToGregorian']);
});
