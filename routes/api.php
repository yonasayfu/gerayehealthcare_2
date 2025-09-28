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
use App\Http\Controllers\Api\V1\PrescriptionController as ApiPrescriptionController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\VisitServiceController as ApiVisitServiceController;
use App\Http\Controllers\Api\V1\ModuleController;
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

        Route::get('/modules', ModuleController::class);

        // Profile endpoints
        Route::get('/me', [UserController::class, 'me']);
        Route::patch('/me', [UserController::class, 'update']);

        Route::get('/users', [UserController::class, 'index'])
            ->middleware('permission:view users')
            ->name('users.index');

        Route::apiResource('patients', PatientController::class)
            ->only(['index', 'show'])
            ->middleware('permission:view patients');

        // Patient self endpoints
        Route::get('/patients/me', [PatientController::class, 'me']);
        Route::patch('/patients/me', [PatientController::class, 'updateMe']);

        // Visit services for mobile staff usage
        Route::get('/visit-services/my-schedule', [ApiVisitServiceController::class, 'mySchedule'])
            ->middleware(['throttle:60,1', 'permission:view visit services']);
        Route::post('/visit-services', [ApiVisitServiceController::class, 'store'])
            ->middleware(['throttle:30,1', 'permission:create visit services']);
        Route::patch('/visit-services/{visitService}', [ApiVisitServiceController::class, 'update'])
            ->middleware(['throttle:30,1', 'permission:edit visit services']);
        Route::delete('/visit-services/{visitService}', [ApiVisitServiceController::class, 'destroy'])
            ->middleware(['throttle:30,1', 'permission:delete visit services']);
        Route::post('/visit-services/{visitService}/check-in', [ApiVisitServiceController::class, 'checkIn'])
            ->middleware(['throttle:20,1', 'permission:edit visit services']);
        Route::post('/visit-services/{visitService}/check-out', [ApiVisitServiceController::class, 'checkOut'])
            ->middleware(['throttle:20,1', 'permission:edit visit services']);

        // Caregiver assignments context
        Route::get('/caregiver-assignments/my-active', [ApiCaregiverAssignmentController::class, 'myActive'])
            ->middleware('permission:view assignments');
        Route::get('/caregiver-assignments/my-patients', [ApiCaregiverAssignmentController::class, 'myPatients'])
            ->middleware('permission:view assignments');

        // Services catalog
        Route::get('/services', [ServiceController::class, 'index'])
            ->middleware('permission:view services');

        // Prescriptions for mobile (doctor/patient self access)
        Route::get('/prescriptions', [ApiPrescriptionController::class, 'index'])
            ->middleware('permission:view prescriptions');
        Route::get('/prescriptions/{prescription}', [ApiPrescriptionController::class, 'show'])
            ->middleware('permission:view prescriptions');

        // Messaging
        Route::get('/messages/threads', [ApiMessageController::class, 'threads'])
            ->middleware(['throttle:60,1', 'permission:view messages']);
        Route::get('/messages/threads/{user}', [ApiMessageController::class, 'thread'])
            ->middleware(['throttle:60,1', 'permission:view messages']);
        Route::post('/messages/threads/{user}/messages', [ApiMessageController::class, 'send'])
            ->middleware(['throttle:30,1', 'permission:send messages']);
        Route::delete('/messages/{message}', [ApiMessageController::class, 'destroy'])
            ->middleware(['throttle:60,1', 'permission:delete messages']);
        Route::get('/messages/{message}/download', [ApiMessageController::class, 'downloadAttachment'])
            ->middleware(['throttle:120,1', 'permission:view messages']);
        Route::post('/messages/bulk-delete', [ApiMessageController::class, 'bulkDestroy'])
            ->middleware(['throttle:60,1', 'permission:delete messages']);
        Route::post('/messages/{message}/pin', [ApiMessageController::class, 'pin'])
            ->middleware(['throttle:60,1', 'permission:send messages']);
        Route::post('/messages/{message}/unpin', [ApiMessageController::class, 'unpin'])
            ->middleware(['throttle:60,1', 'permission:send messages']);
        Route::get('/messages/threads/{user}/search', [ApiMessageController::class, 'search'])
            ->middleware(['throttle:60,1', 'permission:view messages']);
            
        // Enhanced messaging features for modern template
        Route::post('/messages/typing', [ApiMessageController::class, 'typing'])
            ->middleware(['throttle:120,1', 'permission:send messages']);
        Route::get('/messages/online-users', [ApiMessageController::class, 'onlineUsers'])
            ->middleware(['throttle:60,1', 'permission:view messages']);
        Route::post('/messages/{message}/react', [ApiMessageController::class, 'react'])
            ->middleware(['throttle:60,1', 'permission:send messages']);
        Route::delete('/messages/{message}/react', [ApiMessageController::class, 'removeReaction'])
            ->middleware(['throttle:60,1', 'permission:send messages']);
        Route::post('/messages/{message}/forward', [ApiMessageController::class, 'forward'])
            ->middleware(['throttle:30,1', 'permission:send messages']);
        Route::patch('/messages/{message}', [ApiMessageController::class, 'update'])
            ->middleware(['throttle:30,1', 'permission:send messages']);
        Route::post('/messages/{message}/reply', [ApiMessageController::class, 'reply'])
            ->middleware(['throttle:30,1', 'permission:send messages']);
        Route::post('/messages/voice', [ApiMessageController::class, 'sendVoiceMessage'])
            ->middleware(['throttle:20,1', 'permission:send messages']);
        Route::get('/messages/threads/{user}/media', [ApiMessageController::class, 'getMedia'])
            ->middleware(['throttle:60,1', 'permission:view messages']);
        Route::get('/messages/threads/{user}/files', [ApiMessageController::class, 'getFiles'])
            ->middleware(['throttle:60,1', 'permission:view messages']);
        Route::post('/messages/threads/{user}/clear', [ApiMessageController::class, 'clearThread'])
            ->middleware(['throttle:10,1', 'permission:delete messages']);
        Route::post('/messages/{message}/read-receipt', [ApiMessageController::class, 'markAsRead'])
            ->middleware(['throttle:120,1', 'permission:view messages']);

        // Group Messaging (API)
        Route::post('/groups', [GroupMessageController::class, 'createGroup'])
            ->middleware('permission:manage group messages')
            ->name('api.groups.create');
        Route::get('/groups', [GroupMessageController::class, 'getGroups'])
            ->middleware('permission:view messages')
            ->name('api.groups.list');
        Route::get('/groups/{group}/messages', [GroupMessageController::class, 'index'])
            ->middleware('permission:view messages')
            ->name('api.groups.messages.index');
        Route::post('/groups/{group}/messages', [GroupMessageController::class, 'store'])
            ->middleware('permission:manage group messages')
            ->name('api.groups.messages.store');
        Route::patch('/groups/{group}/messages/{message}', [GroupMessageController::class, 'update'])
            ->middleware('permission:manage group messages')
            ->name('api.groups.messages.update');
        Route::delete('/groups/{group}/messages/{message}', [GroupMessageController::class, 'destroy'])
            ->middleware('permission:manage group messages')
            ->name('api.groups.messages.destroy');
        Route::post('/groups/{group}/messages/{message}/react', [GroupMessageController::class, 'react'])
            ->middleware('permission:manage group messages')
            ->name('api.groups.messages.react');
        Route::get('/groups/{group}/messages/{message}/attachment', [GroupMessageController::class, 'downloadAttachment'])
            ->middleware('permission:view messages')
            ->name('api.groups.messages.attachment');
        Route::post('/groups/{group}/messages/{message}/pin', [GroupMessageController::class, 'pin'])
            ->middleware('permission:manage group messages')
            ->name('api.groups.messages.pin');
        Route::post('/groups/{group}/messages/{message}/unpin', [GroupMessageController::class, 'unpin'])
            ->middleware('permission:manage group messages')
            ->name('api.groups.messages.unpin');
        Route::get('/groups/{group}/messages/search', [GroupMessageController::class, 'search'])
            ->middleware('permission:view messages')
            ->name('api.groups.messages.search');

        // Notifications
        Route::get('/notifications', [ApiNotificationController::class, 'index'])
            ->middleware('permission:view notifications');
        Route::post('/notifications/{notification}/read', [ApiNotificationController::class, 'markRead'])
            ->middleware('permission:view notifications');

        // Push tokens
        Route::post('/push-tokens', [PushTokenController::class, 'store']);
        Route::delete('/push-tokens', [PushTokenController::class, 'destroy']);

        // Documents
        Route::get('/documents/my', [ApiDocumentController::class, 'my'])
            ->middleware('permission:view medical documents');
        Route::get('/documents/{document}/download', [ApiDocumentController::class, 'download'])
            ->middleware('permission:download medical documents');
        Route::post('/documents', [ApiDocumentController::class, 'store'])
            ->middleware('permission:create medical documents');

        // Invoices & Insurance (read-only)
        Route::get('/invoices/my', [ApiBillingController::class, 'myInvoices']);
        Route::get('/invoices/{invoice}', [ApiBillingController::class, 'showInvoice'])
            ->middleware('permission:view invoices');
        Route::get('/insurance/policies/my', [ApiBillingController::class, 'myPolicies']);
        Route::get('/insurance/claims/my', [ApiBillingController::class, 'myClaims']);
        Route::get('/insurance/claims/{claim}', [ApiBillingController::class, 'showClaim'])
            ->middleware('permission:view insurance claims');

        // Marketing APIs
        Route::prefix('marketing')->group(function () {
            Route::get('/campaigns', [App\Http\Controllers\Api\V1\MarketingController::class, 'campaigns'])
                ->middleware('permission:view marketing campaigns');
            Route::post('/campaigns', [App\Http\Controllers\Api\V1\MarketingController::class, 'createCampaign'])
                ->middleware('permission:manage marketing');
            Route::put('/campaigns/{campaign}', [App\Http\Controllers\Api\V1\MarketingController::class, 'updateCampaign'])
                ->middleware('permission:manage marketing');
            Route::delete('/campaigns/{campaign}', [App\Http\Controllers\Api\V1\MarketingController::class, 'deleteCampaign'])
                ->middleware('permission:manage marketing');
            Route::get('/campaigns/{campaign}/leads', [App\Http\Controllers\Api\V1\MarketingController::class, 'campaignLeads'])
                ->middleware('permission:view marketing leads');

            Route::get('/leads', [App\Http\Controllers\Api\V1\MarketingController::class, 'leads'])
                ->middleware('permission:view marketing leads');
            Route::post('/leads', [App\Http\Controllers\Api\V1\MarketingController::class, 'createLead'])
                ->middleware('permission:manage marketing');
            Route::post('/leads/{lead}/convert', [App\Http\Controllers\Api\V1\MarketingController::class, 'convertLead'])
                ->middleware('permission:manage marketing');

            Route::get('/analytics', [App\Http\Controllers\Api\V1\MarketingController::class, 'analytics'])
                ->middleware('permission:view marketing analytics');
        });

        // Inventory APIs
        Route::prefix('inventory')->group(function () {
            Route::get('/items', [App\Http\Controllers\Api\V1\InventoryController::class, 'items'])
                ->middleware('permission:view inventory items');
            Route::post('/items', [App\Http\Controllers\Api\V1\InventoryController::class, 'createItem'])
                ->middleware('permission:create inventory items');
            Route::put('/items/{item}', [App\Http\Controllers\Api\V1\InventoryController::class, 'updateItem'])
                ->middleware('permission:update inventory items');
            Route::delete('/items/{item}', [App\Http\Controllers\Api\V1\InventoryController::class, 'deleteItem'])
                ->middleware('permission:delete inventory items');
            Route::post('/items/{item}/adjust-stock', [App\Http\Controllers\Api\V1\InventoryController::class, 'adjustStock'])
                ->middleware('permission:update inventory items');

            Route::get('/requests', [App\Http\Controllers\Api\V1\InventoryController::class, 'requests'])
                ->middleware('permission:view inventory requests');
            Route::post('/requests', [App\Http\Controllers\Api\V1\InventoryController::class, 'createRequest'])
                ->middleware('permission:create inventory requests');

            Route::get('/analytics', [App\Http\Controllers\Api\V1\InventoryController::class, 'analytics'])
                ->middleware('permission:export inventory reports');
        });

        // Insurance APIs
        Route::prefix('insurance')->group(function () {
            Route::get('/companies', [App\Http\Controllers\Api\V1\InsuranceController::class, 'companies'])
                ->middleware('permission:view insurance companies');
            Route::post('/companies', [App\Http\Controllers\Api\V1\InsuranceController::class, 'createCompany'])
                ->middleware('permission:create insurance companies');

            Route::get('/policies', [App\Http\Controllers\Api\V1\InsuranceController::class, 'policies'])
                ->middleware('permission:view insurance policies');
            Route::post('/policies', [App\Http\Controllers\Api\V1\InsuranceController::class, 'createPolicy'])
                ->middleware('permission:create insurance policies');

            Route::get('/claims', [App\Http\Controllers\Api\V1\InsuranceController::class, 'claims'])
                ->middleware('permission:view insurance claims');
            Route::post('/claims', [App\Http\Controllers\Api\V1\InsuranceController::class, 'createClaim'])
                ->middleware('permission:create insurance claims');
            Route::put('/claims/{claim}/status', [App\Http\Controllers\Api\V1\InsuranceController::class, 'updateClaimStatus'])
                ->middleware('permission:update insurance claims');

            Route::get('/analytics', [App\Http\Controllers\Api\V1\InsuranceController::class, 'analytics'])
                ->middleware('permission:view financial analytics');
        });

        // Analytics APIs
        Route::prefix('analytics')->middleware('permission:view analytics dashboard')->group(function () {
            Route::get('/dashboard', [App\Http\Controllers\Api\V1\AnalyticsController::class, 'dashboard']);
            Route::get('/patients', [App\Http\Controllers\Api\V1\AnalyticsController::class, 'patients']);
            Route::get('/visits', [App\Http\Controllers\Api\V1\AnalyticsController::class, 'visits']);
            Route::get('/revenue', [App\Http\Controllers\Api\V1\AnalyticsController::class, 'revenue']);
            Route::get('/staff', [App\Http\Controllers\Api\V1\AnalyticsController::class, 'staff']);
        });

        // Bulk Operations APIs
        Route::prefix('bulk')->middleware('permission:manage users')->group(function () {
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

    // Public: Event Recommendations (guest + staff can submit)
    Route::post('/event-recommendations', [\App\Http\Controllers\Api\V1\EventRecommendationController::class, 'store'])
        ->middleware('throttle:30,1')
        ->name('api.v1.event-recommendations.store');
});
