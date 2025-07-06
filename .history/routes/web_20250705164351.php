<?php
use App\Enums\RoleEnum; // Import our Role Enum
use App\Http\Controllers\Admin\CaregiverAssignmentController;
use App\Http\Controllers\Admin\PatientController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\StaffAvailabilityController;
use App\Http\Controllers\Staff\MyAvailabilityController; // Corrected path
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Home & Dashboard (Publicly accessible for logged-in users)
Route::get('/', function () { return Inertia::render('Welcome'); })->name('home');
Route::get('dashboard', function () { return Inertia::render('Dashboard'); })->middleware(['auth', 'verified'])->name('dashboard');

// --- Admin & Super Admin Routes ---
// These routes are only accessible by users with the 'Super Admin' or 'Admin' role.
Route::middleware(['auth', 'verified', 'role:' . RoleEnum::SUPER_ADMIN->value . '|' . RoleEnum::ADMIN->value])
    ->prefix('dashboard')
    ->name('admin.') // Optional: for route name prefixing
    ->group(function () {

    // Patient Module
    Route::get('patients/export', [PatientController::class, 'export'])->name('patients.export');
    Route::get('patients/validate-field', [PatientController::class, 'validateField'])->name('patients.validate-field');
    Route::resource('patients', PatientController::class)->except(['show']);
    
    // Staff Module
    Route::get('staff/export', [StaffController::class, 'export'])->name('staff.export');
    Route::resource('staff', StaffController::class);

    // Caregiver Assignments Module
    Route::get('assignments/export', [CaregiverAssignmentController::class, 'export'])->name('assignments.export');
    Route::resource('assignments', CaregiverAssignmentController::class);

    // Staff Availability Admin View
    Route::get('staff-availabilities/events', [StaffAvailabilityController::class, 'getCalendarEvents'])->name('staff-availabilities.events');
    Route::resource('staff-availabilities', StaffAvailabilityController::class)->only(['index', 'store', 'update', 'destroy']);
});

// --- Staff-Specific Routes ---
// These routes are only accessible by users with the 'Staff' role.
Route::middleware(['auth', 'verified', 'role:' . RoleEnum::STAFF->value])
    ->prefix('dashboard')
    ->name('staff.') // Optional: for route name prefixing
    ->group(function() {
    
    // My Availability Calendar Page
    Route::get('my-availability', [MyAvailabilityController::class, 'index'])->name('my-availability.index');
});
// Additional modules
require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
