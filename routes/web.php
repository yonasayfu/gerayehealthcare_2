<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Home
Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

// Dashboard root
Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Dashboard modules (grouped)
Route::middleware(['auth', 'verified'])->prefix('dashboard')->group(function () {
    // Patient Management
    Route::view('patients', 'Admin/Patients/Index')->name('patients.index');
    Route::view('assignments', 'Admin/CaregiverAssignments/Index')->name('assignments.index');
    Route::view('visits', 'Admin/VisitServices/Index')->name('visits.index');

    // Admin Tools
    Route::view('staff', 'Admin/Staff/Index')->name('staff.index');
    Route::view('messages', 'Admin/Messages/Index')->name('messages.index');
    Route::view('invoices', 'Admin/Invoices/Index')->name('invoices.index');
    Route::view('insurance', 'Admin/InsuranceClaims/Index')->name('insurance.index');
    Route::view('inventory', 'Admin/InventoryItems/Index')->name('inventory.index');
    Route::view('tasks', 'Admin/AdminTasks/Index')->name('tasks.index');

    // Integrations
    Route::view('partners', 'Admin/PartnerHospitals/Index')->name('partners.index');
    Route::view('referrals', 'Admin/Referrals/Index')->name('referrals.index');
    Route::view('marketing', 'Admin/MarketingCampaigns/Index')->name('marketing.index');
    Route::view('international', 'Admin/InternationalReferrals/Index')->name('international.index');
    Route::view('events', 'Admin/Events/Index')->name('events.index');
    Route::view('networks', 'Admin/NgoNetworks/Index')->name('networks.index');
});

// Include additional modules
require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
