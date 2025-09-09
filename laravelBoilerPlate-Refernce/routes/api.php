<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and assigned to the "api"
| middleware group. Make something great!
|
 */

Route::prefix('v1')->group(function () {
    // Test routes for API base class functionality
    Route::get('/test', [App\Http\Controllers\Api\V1\TestApiController::class, 'index'])->name('api.test.index');
    Route::get('/test/error', [App\Http\Controllers\Api\V1\TestApiController::class, 'error'])->name('api.test.error');

    // Public routes
    Route::post('/auth/login', [\App\Http\Controllers\Api\V1\AuthController::class, 'login']);
    Route::post('/auth/register', [\App\Http\Controllers\Api\V1\AuthController::class, 'register']);
    Route::post('/auth/forgot-password', [\App\Http\Controllers\Api\V1\AuthController::class, 'forgotPassword']);
    Route::post('/auth/reset-password', [\App\Http\Controllers\Api\V1\AuthController::class, 'resetPassword']);

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        // Authentication
        Route::post('/auth/logout', [\App\Http\Controllers\Api\V1\AuthController::class, 'logout']);
        Route::post('/auth/refresh', [\App\Http\Controllers\Api\V1\AuthController::class, 'refresh']);
        Route::get('/auth/user', [\App\Http\Controllers\Api\V1\AuthController::class, 'user']);
        Route::get('/auth/tokens', [\App\Http\Controllers\Api\V1\AuthController::class, 'tokens']);
        Route::delete('/auth/tokens/{token}', [\App\Http\Controllers\Api\V1\AuthController::class, 'revokeToken']);

        // User management
        Route::apiResource('users', \App\Http\Controllers\Api\V1\UserApiController::class);
        Route::get('/users/{user}/permissions', [\App\Http\Controllers\Api\V1\UserApiController::class, 'permissions']);
        Route::post('/users/{user}/assign-role', [\App\Http\Controllers\Api\V1\UserApiController::class, 'assignRole']);
        Route::post('/users/{user}/remove-role', [\App\Http\Controllers\Api\V1\UserApiController::class, 'removeRole']);

        // Staff management
        Route::apiResource('staff', \App\Http\Controllers\Api\V1\StaffApiController::class);
        Route::get('/staff/dropdown', [\App\Http\Controllers\Api\V1\StaffApiController::class, 'dropdown']);
        Route::get('/staff/search', [\App\Http\Controllers\Api\V1\StaffApiController::class, 'search']);
        Route::get('/staff/available', [\App\Http\Controllers\Api\V1\StaffApiController::class, 'available']);
        Route::get('/staff/stats', [\App\Http\Controllers\Api\V1\StaffApiController::class, 'stats']);
        Route::get('/staff/departments', [\App\Http\Controllers\Api\V1\StaffApiController::class, 'departments']);
        Route::get('/staff/positions', [\App\Http\Controllers\Api\V1\StaffApiController::class, 'positions']);
        Route::post('/staff/{staff}/upload-photo', [\App\Http\Controllers\Api\V1\StaffApiController::class, 'uploadPhoto']);

        // Messages
        Route::get('/messages/threads', [\App\Http\Controllers\Api\V1\MessageController::class, 'threads']);
        Route::get('/messages/threads/{user}', [\App\Http\Controllers\Api\V1\MessageController::class, 'thread']);
        Route::post('/messages/threads/{user}/messages', [\App\Http\Controllers\Api\V1\MessageController::class, 'send']);
        Route::post('/messages/{message}/read', [\App\Http\Controllers\Api\V1\MessageController::class, 'markRead']);
        Route::post('/messages/{message}/unread', [\App\Http\Controllers\MessageController::class, 'markAsUnread']);
        Route::delete('/messages/{message}', [\App\Http\Controllers\Api\V1\MessageController::class, 'destroy']);
        Route::get('/messages/unread/count', [\App\Http\Controllers\Api\V1\MessageController::class, 'unreadCount']);

        // Message Reactions
        Route::post('/messages/{message}/react', [\App\Http\Controllers\MessageController::class, 'react']);
        Route::delete('/messages/{message}/react', [\App\Http\Controllers\MessageController::class, 'removeReaction']);

        // Typing Indicators
        Route::post('/messages/typing', [\App\Http\Controllers\MessageController::class, 'typing']);
        Route::get('/messages/typing/{user}', [\App\Http\Controllers\MessageController::class, 'typingStatus']);

        // Message Export
        Route::get('/messages/export/{user}/csv', [\App\Http\Controllers\MessageController::class, 'exportThreadCsv']);

        // Groups
        Route::apiResource('groups', \App\Http\Controllers\GroupController::class);
        Route::post('/groups/{group}/members', [\App\Http\Controllers\GroupController::class, 'addMember']);
        Route::delete('/groups/{group}/members/{user}', [\App\Http\Controllers\GroupController::class, 'removeMember']);
        Route::post('/groups/{group}/leave', [\App\Http\Controllers\GroupController::class, 'leave']);

        // Notifications
        Route::get('/notifications', [\App\Http\Controllers\Api\V1\NotificationController::class, 'index']);
        Route::get('/notifications/unread', [\App\Http\Controllers\Api\V1\NotificationController::class, 'unread']);
        Route::post('/notifications/{notification}/read', [\App\Http\Controllers\Api\V1\NotificationController::class, 'markRead']);
        Route::post('/notifications/{notification}/unread', [\App\Http\Controllers\Api\V1\NotificationController::class, 'markUnread']);
        Route::post('/notifications/mark-all-read', [\App\Http\Controllers\Api\V1\NotificationController::class, 'markAllRead']);
        Route::delete('/notifications/{notification}', [\App\Http\Controllers\Api\V1\NotificationController::class, 'destroy']);
        Route::delete('/notifications/delete-all-read', [\App\Http\Controllers\Api\V1\NotificationController::class, 'deleteAllRead']);
        Route::get('/notifications/stats', [\App\Http\Controllers\Api\V1\NotificationController::class, 'stats']);
        Route::post('/notifications/preferences', [\App\Http\Controllers\Api\V1\NotificationController::class, 'updatePreferences']);

        // Global Search
        Route::get('/search', [\App\Http\Controllers\Admin\GlobalSearchController::class, 'search']);
        Route::get('/search/suggestions', [\App\Http\Controllers\Admin\GlobalSearchController::class, 'suggestions']);
        Route::get('/search/stats', [\App\Http\Controllers\Admin\GlobalSearchController::class, 'getStats']);
        Route::get('/search/entities', [\App\Http\Controllers\Admin\GlobalSearchController::class, 'getEntities']);
    });
});
