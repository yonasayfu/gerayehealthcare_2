<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\Rbac\DashboardController as RbacDashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// RBAC Routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/rbac/dashboard', [RbacDashboardController::class, 'index'])
        ->middleware('can:view-reports')
        ->name('rbac.dashboard');
    Route::get('/rbac/roles/{roleName}', [RbacDashboardController::class, 'showRole'])
        ->middleware('can:view-roles')
        ->name('rbac.roles.show');
});

// Enhanced RBAC Management Routes
Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    // Role Management
    Route::resource('roles', \App\Http\Controllers\Admin\RoleController::class)
        ->middleware('can:manage-roles');
    Route::post('roles/{role}/assign-user', [\App\Http\Controllers\Admin\RoleController::class, 'assignToUser'])
        ->name('roles.assign-user');
    Route::post('roles/{role}/remove-user', [\App\Http\Controllers\Admin\RoleController::class, 'removeFromUser'])
        ->name('roles.remove-user');
    Route::post('roles/initialize-defaults', [\App\Http\Controllers\Admin\RoleController::class, 'initializeDefaults'])
        ->name('roles.initialize-defaults');

    // Staff Management
    Route::resource('staff', \App\Http\Controllers\Admin\StaffController::class);
    Route::get('staff/export', [\App\Http\Controllers\Admin\StaffController::class, 'export'])
        ->name('staff.export');
    Route::get('staff/print', [\App\Http\Controllers\Admin\StaffController::class, 'print'])
        ->name('staff.print');
    Route::post('staff/{staff}/upload-photo', [\App\Http\Controllers\Admin\StaffController::class, 'uploadPhoto'])
        ->name('staff.upload-photo');

    // Global Search
    Route::get('global-search', [\App\Http\Controllers\Admin\GlobalSearchController::class, 'search'])
        ->name('global-search');
    Route::get('search-suggestions', [\App\Http\Controllers\Admin\GlobalSearchController::class, 'suggestions'])
        ->name('search-suggestions');
    Route::get('search-stats', [\App\Http\Controllers\Admin\GlobalSearchController::class, 'getStats'])
        ->name('search-stats');
    Route::get('search-entities', [\App\Http\Controllers\Admin\GlobalSearchController::class, 'getEntities'])
        ->name('search-entities');
});

// Messaging Routes
Route::middleware(['auth', 'verified'])->group(function () {
    // Basic messaging
    Route::get('/messages', [\App\Http\Controllers\MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/data/{recipient?}', [\App\Http\Controllers\MessageController::class, 'getData'])->name('messages.data');
    Route::post('/messages', [\App\Http\Controllers\MessageController::class, 'store'])->name('messages.store');
    Route::get('/messages/{message}', [\App\Http\Controllers\MessageController::class, 'show'])->name('messages.show');
    Route::delete('/messages/{message}', [\App\Http\Controllers\MessageController::class, 'destroy'])->name('messages.destroy');
    Route::post('/messages/{message}/read', [\App\Http\Controllers\MessageController::class, 'markAsRead'])->name('messages.mark-read');
    Route::post('/messages/{message}/unread', [\App\Http\Controllers\MessageController::class, 'markAsUnread'])->name('messages.mark-unread');
    Route::get('/messages/unread/count', [\App\Http\Controllers\MessageController::class, 'getUnreadCount'])->name('messages.unread-count');

    // Message reactions
    Route::post('/messages/{message}/react', [\App\Http\Controllers\MessageController::class, 'react'])->name('messages.react');
    Route::delete('/messages/{message}/react', [\App\Http\Controllers\MessageController::class, 'removeReaction'])->name('messages.remove-reaction');

    // Typing indicators
    Route::post('/messages/typing', [\App\Http\Controllers\MessageController::class, 'typing'])->name('messages.typing');
    Route::get('/messages/typing/{user}', [\App\Http\Controllers\MessageController::class, 'typingStatus'])->name('messages.typing-status');

    // Message export
    Route::get('/messages/export/{user}/csv', [\App\Http\Controllers\MessageController::class, 'exportThreadCsv'])->name('messages.export-csv');

    // Groups
    Route::resource('groups', \App\Http\Controllers\GroupController::class);
    Route::post('/groups/{group}/members', [\App\Http\Controllers\GroupController::class, 'addMember'])->name('groups.add-member');
    Route::delete('/groups/{group}/members/{user}', [\App\Http\Controllers\GroupController::class, 'removeMember'])->name('groups.remove-member');
    Route::post('/groups/{group}/leave', [\App\Http\Controllers\GroupController::class, 'leave'])->name('groups.leave');
});

// Notification Routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/unread', [\App\Http\Controllers\NotificationController::class, 'getUnread'])->name('notifications.unread');
    Route::post('/notifications/{notification}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::post('/notifications/{notification}/unread', [\App\Http\Controllers\NotificationController::class, 'markAsUnread'])->name('notifications.mark-unread');
    Route::post('/notifications/mark-all-read', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::delete('/notifications/{notification}', [\App\Http\Controllers\NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::delete('/notifications/delete-all-read', [\App\Http\Controllers\NotificationController::class, 'deleteAllRead'])->name('notifications.delete-all-read');
    Route::get('/notifications/stats', [\App\Http\Controllers\NotificationController::class, 'getStats'])->name('notifications.stats');
    Route::post('/notifications/preferences', [\App\Http\Controllers\NotificationController::class, 'updatePreferences'])->name('notifications.preferences');
});

require __DIR__.'/auth.php';
