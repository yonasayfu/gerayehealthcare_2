<?php
use App\Http\Controllers\Admin\CaregiverAssignmentController;
use App\Http\Controllers\Admin\PatientController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\StaffAvailabilityController; // Add this import
use App\Http\Controllers\Staff\MyAvailabilityController;

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Home
Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

// Dashboard
Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Modules under /dashboard
Route::middleware(['auth', 'verified'])->prefix('dashboard')->group(function () {

    // 🩺 Patient Module
    Route::resource('patients', PatientController::class)->except(['show']);
    Route::get('patients/export', [PatientController::class, 'export'])->name('patients.export');
    Route::get('patients/validate-field', [PatientController::class, 'validateField'])->name('patients.validate-field');
    Route::get('staff/export', [StaffController::class, 'export'])->name('staff.export'); // (optional)
    // 👨‍⚕️ Staff Module (FIXED)
    Route::resource('staff', StaffController::class); // ✅ consistent with 'patients'
    // 🗓️ Caregiver Assignments Module (FIXED ORDER)
    Route::get('assignments/export', [CaregiverAssignmentController::class, 'export'])->name('assignments.export');
    Route::resource('assignments', CaregiverAssignmentController::class);

    // ✨ Staff Availability Module (NEW)
    // Specific route for fetching calendar events MUST come before the resource route.
    Route::get('staff-availabilities/events', [StaffAvailabilityController::class, 'getCalendarEvents'])->name('staff-availabilities.events');
    // Resource route for the admin list view and CRUD actions.
    Route::resource('staff-availabilities', StaffAvailabilityController::class)->only(['index', 'store', 'update', 'destroy']);
   // My Availability Page Route - Points to the new Staff controller
    Route::get('my-availability', [MyAvailabilityController::class, 'index'])->name('my-availability.index');

});

// Additional modules
require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
