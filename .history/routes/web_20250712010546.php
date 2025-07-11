<?php

use App\Enums\RoleEnum;
use App\Http\Controllers\Admin\CaregiverAssignmentController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\LeaveRequestController as AdminLeaveRequestController;
use App\Http\Controllers\Admin\PatientController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\StaffAvailabilityController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\StaffPayoutController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VisitServiceController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Staff\DashboardController as StaffDashboardController;
use App\Http\Controllers\Staff\LeaveRequestController as StaffLeaveRequestController;
use App\Http\Controllers\Staff\MyAvailabilityController;
use App\Http\Controllers\Staff\MyEarningsController;
use App\Http\Controllers\Staff\MyVisitController;
use App\Http\Controllers\Admin\TaskDelegationController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// --- Public & General Routes ---
Route::get('/', function () { return Inertia::render('Welcome'); })->name('home');

// --- Role-based Dashboard Route ---
Route::get('dashboard', function () {
    $user = Auth::user();

    if ($user->hasAnyRole([RoleEnum::SUPER_ADMIN->value, RoleEnum::ADMIN->value])) {
        return app(AdminDashboardController::class)->index();
    }
    elseif ($user->hasRole(RoleEnum::STAFF->value)) {
        return app(StaffDashboardController::class)->index();
    }
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


// --- Messaging & Notification Routes (for all authenticated users) ---
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/messages/{recipient?}', [MessageController::class, 'index'])->name('messages.index');
    Route::post('/messages', [MessageController::class, 'store'])->name('messages.store');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
});

// --- Admin & Super Admin Routes ---
Route::middleware(['auth', 'verified', 'role:' . RoleEnum::SUPER_ADMIN->value . '|' . RoleEnum::ADMIN->value])
    ->prefix('dashboard')
    ->name('admin.')
    ->group(function () {
        // ... (All other admin routes remain the same)
        Route::get('patients/export', [PatientController::class, 'export'])->name('patients.export');
        Route::resource('patients', PatientController::class);
        Route::resource('assignments', CaregiverAssignmentController::class);
        Route::resource('visit-services', VisitServiceController::class);
        Route::get('staff/export', [StaffController::class, 'export'])->name('staff.export');
        Route::resource('staff', StaffController::class);
        Route::get('staff-availabilities/events', [StaffAvailabilityController::class, 'getCalendarEvents'])->name('staff-availabilities.events');
        Route::resource('staff-availabilities', StaffAvailabilityController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::get('staff-payouts', [StaffPayoutController::class, 'index'])->name('staff-payouts.index');
        Route::post('staff-payouts', [StaffPayoutController::class, 'store'])->name('staff-payouts.store');
        Route::resource('invoices', InvoiceController::class)->except(['edit', 'update', 'destroy']);
        Route::resource('services', ServiceController::class);

        // --- Admin Leave Request Routes ---
        // This registers 'admin.admin-leave-requests.index' (GET) and 'admin.admin-leave-requests.update' (PUT/PATCH)
        // All middleware from the group will apply to these routes.
        Route::resource('admin-leave-requests', AdminLeaveRequestController::class)
     ->parameters(['admin-leave-requests' => 'leave_request'])
     ->only(['index', 'update']);

     Route::get('task-delegations/export', [TaskDelegationController::class, 'export'])
     ->name('task-delegations.export');

// 2) Resource routes WITHOUT show
Route::resource('task-delegations', TaskDelegationController::class)
     ->parameters(['task-delegations' => 'task_delegation'])
     ->except(['show']);
        // System Management (Super Admin Only)
        Route::middleware('role:' . RoleEnum::SUPER_ADMIN->value)->group(function () {
            Route::resource('roles', RoleController::class);
            Route::resource('users', UserController::class);
        });
    });

// --- Staff-Specific Routes ---
Route::middleware(['auth', 'verified', 'role:' . RoleEnum::STAFF->value])
    ->prefix('dashboard')
    ->name('staff.')
    ->group(function () {
        // ... (All other staff routes remain the same)
        Route::get('my-availability', [MyAvailabilityController::class, 'index'])->name('my-availability.index');
        Route::get('my-availability/events', [MyAvailabilityController::class, 'getEvents'])->name('my-availability.events');
        Route::post('my-availability', [MyAvailabilityController::class, 'store'])->name('my-availability.store');
        Route::put('my-availability/{availability}', [MyAvailabilityController::class, 'update'])->name('my-availability.update');
        Route::delete('my-availability/{availability}', [MyAvailabilityController::class, 'destroy'])->name('my-availability.destroy');
        Route::get('my-visits', [MyVisitController::class, 'index'])->name('my-visits.index');
        Route::post('my-visits/{visit}/check-in', [MyVisitController::class, 'checkIn'])->name('my-visits.check-in');
        Route::post('my-visits/{visit}/check-out', [MyVisitController::class, 'checkOut'])->name('my-visits.check-out');
        Route::get('my-visits/{visit}/report', [MyVisitController::class, 'showReportForm'])->name('my-visits.report.create');
        Route::post('my-visits/{visit}/report', [MyVisitController::class, 'storeReport'])->name('my-visits.report.store');
        Route::get('my-earnings', [MyEarningsController::class, 'index'])->name('my-earnings.index');
// --- Standard Leave Request Route for Staff ---
Route::resource('leave-requests', StaffLeaveRequestController::class)->only(['index', 'store']);
Route::get('my-tasks', [\App\Http\Controllers\Staff\TaskDelegationController::class, 'index'])->name('task-delegations.index');
        
    });

// --- Auth & Settings Routes ---
require __DIR__ . '/auth.php';
require __DIR__ . '/settings.php';