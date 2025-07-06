<?php

use App\Enums\RoleEnum;
use App\Http\Controllers\Admin\CaregiverAssignmentController;
use App\Http\Controllers\Admin\PatientController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\StaffAvailabilityController;
use App\Http\Controllers\Staff\MyAvailabilityController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () { return Inertia::render('Welcome'); })->name('home');
Route::get('dashboard', function () { return Inertia::render('Dashboard'); })->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified', 'role:' . RoleEnum::SUPER_ADMIN->value . '|' . RoleEnum::ADMIN->value])
    ->prefix('dashboard')
    ->name('admin.')
    ->group(function () {
        Route::get('patients/export', [PatientController::class, 'export'])->name('patients.export');
        Route::resource('patients', PatientController::class)->except(['show']);
        Route::get('staff/export', [StaffController::class, 'export'])->name('staff.export');
        Route::resource('staff', StaffController::class);
        Route::get('assignments/export', [CaregiverAssignmentController::class, 'export'])->name('assignments.export');
        Route::resource('assignments', CaregiverAssignmentController::class);
        Route::get('staff-availabilities/events', [StaffAvailabilityController::class, 'getCalendarEvents'])->name('staff-availabilities.events');
        Route::resource('staff-availabilities', StaffAvailabilityController::class)->only(['index', 'store', 'update', 'destroy']);
});

Route::middleware(['auth', 'verified', 'role:' . RoleEnum::STAFF->value])
    ->prefix('dashboard')
    ->name('staff.')
    ->group(function() {
    Route::get('my-availability', [MyAvailabilityController::class, 'index'])->name('my-availability.index');
});

// Additional modules
require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
