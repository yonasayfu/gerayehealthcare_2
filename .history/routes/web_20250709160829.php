<?php

use App\Enums\RoleEnum;
use App\Http\Controllers\Admin\CaregiverAssignmentController;
use App\Http\Controllers\Admin\PatientController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\StaffAvailabilityController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VisitServiceController;
use App\Http\Controllers\PlaceholderController;
use App\Http\Controllers\Staff\MyAvailabilityController;
use App\Http\Controllers\Staff\MyVisitController;
use App\Http\Controllers\MessageController; 
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Admin\StaffPayoutController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {return Inertia::render('Welcome');})->name('home');
Route::get('dashboard', function () {return Inertia::render('Dashboard');})->middleware(['auth', 'verified'])->name('dashboard');
Route::get('/messages/{recipient?}', [MessageController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('messages.index');

Route::post('/messages', [MessageController::class, 'store'])
    ->middleware(['auth', 'verified'])
    ->name('messages.store');
// --- Notification API Routes ---
Route::get('/notifications', [NotificationController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('notifications.index');

Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])
    ->middleware(['auth', 'verified'])
    ->name('notifications.markAsRead');


Route::middleware(['auth', 'verified', 'role:' . RoleEnum::SUPER_ADMIN->value . '|' . RoleEnum::ADMIN->value])
    ->prefix('dashboard')
    ->name('admin.')
    ->group(function () {
        // --- BUILT MODULES ---
        Route::get('patients/export', [PatientController::class, 'export'])->name('patients.export');
        Route::resource('patients', PatientController::class);

        Route::get('staff/export', [StaffController::class, 'export'])->name('staff.export');
        Route::resource('staff', StaffController::class);

        Route::get('assignments/export', [CaregiverAssignmentController::class, 'export'])->name('assignments.export');
        Route::resource('assignments', CaregiverAssignmentController::class);

        Route::get('staff-availabilities/events', [StaffAvailabilityController::class, 'getCalendarEvents'])->name('staff-availabilities.events');
        Route::resource('staff-availabilities', StaffAvailabilityController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::get('visit-services/export', [VisitServiceController::class, 'export'])->name('visit-services.export');
        Route::resource('visit-services', VisitServiceController::class);

          // --- Staff Payouts Routes ---
        Route::get('staff-payouts', [StaffPayoutController::class, 'index'])->name('staff-payouts.index');
        Route::post('staff-payouts', [StaffPayoutController::class, 'store'])->name('staff-payouts.store');
   
        // --- ROLE & USER MANAGEMENT (Super Admin Only) ---
        Route::resource('roles', RoleController::class)->middleware('role:' . RoleEnum::SUPER_ADMIN->value);
        Route::resource('users', UserController::class)->middleware('role:' . RoleEnum::SUPER_ADMIN->value);

        // --- PLACEHOLDER ROUTES FOR FUTURE MODULES ---
        // Route::get('visits', [PlaceholderController::class, 'index'])->name('visits.index');
        Route::get('messages', [PlaceholderController::class, 'index'])->name('messages.index');
        Route::get('invoices', [PlaceholderController::class, 'index'])->name('invoices.index');
        Route::get('insurance', [PlaceholderController::class, 'index'])->name('insurance.index');
        Route::get('inventory', [PlaceholderController::class, 'index'])->name('inventory.index');
        Route::get('tasks', [PlaceholderController::class, 'index'])->name('tasks.index');
        Route::get('partners', [PlaceholderController::class, 'index'])->name('partners.index');
        Route::get('referrals', [PlaceholderController::class, 'index'])->name('referrals.index');
        Route::get('marketing', [PlaceholderController::class, 'index'])->name('marketing.index');
        Route::get('international', [PlaceholderController::class, 'index'])->name('international.index');
        Route::get('events', [PlaceholderController::class, 'index'])->name('events.index');
        Route::get('networks', [PlaceholderController::class, 'index'])->name('networks.index');
    });

Route::middleware(['auth', 'verified', 'role:' . RoleEnum::STAFF->value])
    ->prefix('dashboard')
    ->name('staff.')
    ->group(function () {
        // The page to view the calendar
        Route::get('my-availability', [MyAvailabilityController::class, 'index'])->name('my-availability.index');

        // THIS IS THE NEW ROUTE TO FIX THE ERROR
        Route::get('my-availability/events', [MyAvailabilityController::class, 'getEvents'])->name('my-availability.events');

        // The routes for the calendar to interact with
        Route::post('my-availability', [MyAvailabilityController::class, 'store'])->name('my-availability.store');
        Route::put('my-availability/{availability}', [MyAvailabilityController::class, 'update'])->name('my-availability.update');
        Route::delete('my-availability/{availability}', [MyAvailabilityController::class, 'destroy'])->name('my-availability.destroy');
        // --- Staff's "My Visits" Routes ---
        Route::get('my-visits', [MyVisitController::class, 'index'])->name('my-visits.index');
        Route::post('my-visits/{visit}/check-in', [MyVisitController::class, 'checkIn'])->name('my-visits.check-in');
        Route::post('my-visits/{visit}/check-out', [MyVisitController::class, 'checkOut'])->name('my-visits.check-out');

    });
// Additional modules
require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
