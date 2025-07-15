<?php

use App\Enums\RoleEnum;
use App\Http\Controllers\Admin\CaregiverAssignmentController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\InvoiceController;

// Admin Controllers
use App\Http\Controllers\Admin\LeaveRequestController as AdminLeaveRequestController;
use App\Http\Controllers\Admin\PatientController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\StaffAvailabilityController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\StaffPayoutController;
use App\Http\Controllers\Admin\TaskDelegationController as AdminTaskController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VisitServiceController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Staff\DashboardController as StaffDashboardController;

// Staff Controllers
use App\Http\Controllers\Staff\LeaveRequestController as StaffLeaveRequestController;
use App\Http\Controllers\Staff\MyAvailabilityController;
use App\Http\Controllers\Staff\MyEarningsController;
use App\Http\Controllers\Staff\MyVisitController;
use App\Http\Controllers\Staff\TaskDelegationController as StaffTaskController;
use Illuminate\Support\Facades\Auth;

// Common Controllers
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
 */

// Public & General
Route::get('/', fn() => Inertia::render('Welcome'))->name('home');

// Shared Dashboard
Route::get('dashboard', function () {
    $user = Auth::user();
    if ($user->hasAnyRole([RoleEnum::SUPER_ADMIN->value, RoleEnum::ADMIN->value])) {
        return app(AdminDashboardController::class)->index();
    } elseif ($user->hasRole(RoleEnum::STAFF->value)) {
        return app(StaffDashboardController::class)->index();
    }
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Messaging & Notifications
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/messages/data', [MessageController::class, 'getData'])->name('messages.data');
    
    Route::post('/messages', [MessageController::class, 'store'])->name('messages.store');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])
        ->name('notifications.markAsRead');
});

// Admin & Super Admin
Route::middleware(['auth', 'verified', 'role:' . RoleEnum::SUPER_ADMIN->value . '|' . RoleEnum::ADMIN->value])
    ->prefix('dashboard')
    ->name('admin.')
    ->group(function () {
        // Patients
        Route::get('patients/export', [PatientController::class, 'export'])->name('patients.export');

        // CORRECTED print-all route:
        // - URI: 'patients/print-all' (combined with group prefix 'dashboard' -> /dashboard/patients/print-all)
        // - Name: 'patients.printAll' (combined with group name 'admin.' -> admin.patients.printAll)
        Route::get('patients/print-all', [PatientController::class, 'printAll'])->name('patients.printAll');
        Route::get('patients/print-current', [PatientController::class, 'printCurrent'])->name('patients.printCurrent');

        // printSingle route (already correctly defined relative to the group)
        Route::get('patients/{patient}/print', [PatientController::class, 'printSingle'])->name('patients.print');

        // Resource route (already correctly defined relative to the group)
        Route::resource('patients', PatientController::class);

        // Caregiver Assignments
        Route::get('assignments/export', [CaregiverAssignmentController::class, 'export'])->name('assignments.export');
        Route::get('assignments/print-all', [CaregiverAssignmentController::class, 'printAll'])->name('assignments.printAll');
        Route::get('assignments/{assignment}/print', [CaregiverAssignmentController::class, 'printSingle'])->name('assignments.print');
        Route::resource('assignments', CaregiverAssignmentController::class);

        // Visit Services
        Route::resource('visit-services', VisitServiceController::class);

        // Staff
        Route::get('staff/export', [StaffController::class, 'export'])->name('staff.export');
        Route::resource('staff', StaffController::class);

        // Staff Availabilities
        Route::get('staff-availabilities/events', [StaffAvailabilityController::class, 'getCalendarEvents'])
            ->name('staff-availabilities.events');
        Route::resource('staff-availabilities', StaffAvailabilityController::class)
            ->only(['index', 'store', 'update', 'destroy']);

        // Staff Payouts
        Route::get('staff-payouts', [StaffPayoutController::class, 'index'])->name('staff-payouts.index');
        Route::post('staff-payouts', [StaffPayoutController::class, 'store'])->name('staff-payouts.store');

        // Invoices
        Route::resource('invoices', InvoiceController::class)
            ->except(['edit', 'update', 'destroy']);

        // Services
        Route::resource('services', ServiceController::class);

        // Admin Leave Requests
        Route::resource('admin-leave-requests', AdminLeaveRequestController::class)
            ->parameters(['admin-leave-requests' => 'leave_request'])
            ->only(['index', 'update']);

        // Task Delegations (Admin)
        Route::get('task-delegations/export', [AdminTaskController::class, 'export'])
            ->name('task-delegations.export');
        Route::resource('task-delegations', AdminTaskController::class)
            ->parameters(['task-delegations' => 'task_delegation'])
            ->except(['show']);

        // System Management (Super Admin only)
        Route::middleware('role:' . RoleEnum::SUPER_ADMIN->value)->group(function () {
            Route::resource('roles', RoleController::class);
            Route::resource('users', UserController::class);
        });
    });

// Staff-Specific
Route::middleware(['auth', 'verified', 'role:' . RoleEnum::STAFF->value])
    ->prefix('dashboard')
    ->name('staff.')
    ->group(function () {
        // My Availability
        Route::get('my-availability', [MyAvailabilityController::class, 'index'])
            ->name('my-availability.index');
        Route::get('my-availability/events', [MyAvailabilityController::class, 'getEvents'])
            ->name('my-availability.events');
        Route::post('my-availability', [MyAvailabilityController::class, 'store'])
            ->name('my-availability.store');
        Route::put('my-availability/{availability}', [MyAvailabilityController::class, 'update'])
            ->name('my-availability.update');
        Route::delete('my-availability/{availability}', [MyAvailabilityController::class, 'destroy'])
            ->name('my-availability.destroy');

        // My Visits
        Route::get('my-visits', [MyVisitController::class, 'index'])->name('my-visits.index');
        Route::post('my-visits/{visit}/check-in', [MyVisitController::class, 'checkIn'])
            ->name('my-visits.check-in');
        Route::post('my-visits/{visit}/check-out', [MyVisitController::class, 'checkOut'])
            ->name('my-visits.check-out');
        Route::get('my-visits/{visit}/report', [MyVisitController::class, 'showReportForm'])
            ->name('my-visits.report.create');
        Route::post('my-visits/{visit}/report', [MyVisitController::class, 'storeReport'])
            ->name('my-visits.report.store');

        // My Earnings
        Route::get('my-earnings', [MyEarningsController::class, 'index'])->name('my-earnings.index');

        // Staff Leave Requests
        Route::resource('leave-requests', StaffLeaveRequestController::class)
            ->only(['index', 'store']);

        // Staff Task Delegations (now on /dashboard/my-tasks)
        Route::get('my-tasks', [StaffTaskController::class, 'index'])
            ->name('task-delegations.index');
        Route::post('my-tasks', [StaffTaskController::class, 'store'])
            ->name('task-delegations.store');
        Route::patch('my-tasks/{task_delegation}', [StaffTaskController::class, 'update'])
            ->name('task-delegations.update');
    });

// Auth & Settings
require __DIR__ . '/auth.php';
require __DIR__ . '/settings.php';
