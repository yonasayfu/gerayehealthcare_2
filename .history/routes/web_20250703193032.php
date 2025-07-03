<?php
use App\Http\Controllers\Admin\PatientController;
use App\Http\Controllers\Admin\StaffController;
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

    // Static views (optional for other tools, not staff)
    // Route::view('assignments', 'Admin/CaregiverAssignments/Index')->name('assignments.index');
    // Route::view('visits', 'Admin/VisitServices/Index')->name('visits.index');

    // Route::view('messages', 'Admin/Messages/Index')->name('messages.index');
    // Route::view('invoices', 'Admin/Invoices/Index')->name('invoices.index');
    // Route::view('insurance', 'Admin/InsuranceClaims/Index')->name('insurance.index');
    // Route::view('inventory', 'Admin/InventoryItems/Index')->name('inventory.index');
    // Route::view('tasks', 'Admin/AdminTasks/Index')->name('tasks.index');

    // Route::view('partners', 'Admin/PartnerHospitals/Index')->name('partners.index');
    // Route::view('referrals', 'Admin/Referrals/Index')->name('referrals.index');
    // Route::view('marketing', 'Admin/MarketingCampaigns/Index')->name('marketing.index');
    // Route::view('international', 'Admin/InternationalReferrals/Index')->name('international.index');
    // Route::view('events', 'Admin/Events/Index')->name('events.index');
    // Route::view('networks', 'Admin/NgoNetworks/Index')->name('networks.index');
});

// Additional modules
require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
