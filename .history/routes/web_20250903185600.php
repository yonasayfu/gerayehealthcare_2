<?php

use App\Enums\RoleEnum;
use App\Http\Controllers\Admin\CaregiverAssignmentController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\EligibilityCriteriaController;
use App\Http\Controllers\Admin\EventBroadcastController;
// Admin Controllers
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\EventParticipantController;
use App\Http\Controllers\Admin\EventRecommendationController;
use App\Http\Controllers\Admin\EventStaffAssignmentController;
use App\Http\Controllers\Admin\GlobalSearchController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\LeaveRequestController as AdminLeaveRequestController;
use App\Http\Controllers\Admin\MedicalDocumentController;
use App\Http\Controllers\Admin\PatientController;
use App\Http\Controllers\Admin\PrescriptionController;
use App\Http\Controllers\Admin\ReferralDocumentController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\SharedInvoiceController;
use App\Http\Controllers\Admin\StaffAvailabilityController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\StaffPayoutController;
use App\Http\Controllers\Admin\TaskDelegationController as AdminTaskController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VisitServiceController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\GroupMessageController;
use App\Http\Controllers\MessageController;
// Staff Controllers
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Staff\DashboardController as StaffDashboardController;
use App\Http\Controllers\Staff\LeaveRequestController as StaffLeaveRequestController;
use App\Http\Controllers\Staff\MyAvailabilityController;
use App\Http\Controllers\Staff\MyEarningsController;
use App\Http\Controllers\Staff\MyVisitController;
use App\Http\Controllers\Staff\TaskDelegationController as StaffTaskController;
use Illuminate\Http\Request;
// Common Controllers
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
 */

// Public & General
Route::get('/', fn () => Inertia::render('Welcome'))->name('home');

// Backward-compatible redirect: /admin -> /dashboard
Route::get('/admin', function () {
    return redirect()->route('dashboard');
})->name('admin.legacy_redirect');

// Public signed invoice PDF (no auth, signed URL required)
Route::get('public/invoices/{invoice}/pdf', [InvoiceController::class, 'publicPdf'])
    ->middleware('signed')
    ->name('invoices.public_pdf');

// Performance testing route
Route::get('/performance-test', function (Request $request) {
    $startTime = microtime(true);
    $queryCount = 0;

    // Listen to queries
    DB::listen(function ($query) use (&$queryCount) {
        $queryCount++;
    });

    $results = [];

    // Test 1: Simple database connection
    $testStart = microtime(true);
    try {
        DB::connection()->getPdo();
        $results['database_connection'] = [
            'status' => 'success',
            'time' => round((microtime(true) - $testStart) * 1000, 2).' ms',
        ];
    } catch (Exception $e) {
        $results['database_connection'] = [
            'status' => 'error',
            'error' => $e->getMessage(),
            'time' => round((microtime(true) - $testStart) * 1000, 2).' ms',
        ];
    }

    // Test 2: Basic model query
    $testStart = microtime(true);
    try {
        $patientCount = \App\Models\Patient::count();
        $results['basic_query'] = [
            'status' => 'success',
            'result' => "Found {$patientCount} patients",
            'time' => round((microtime(true) - $testStart) * 1000, 2).' ms',
        ];
    } catch (Exception $e) {
        $results['basic_query'] = [
            'status' => 'error',
            'error' => $e->getMessage(),
            'time' => round((microtime(true) - $testStart) * 1000, 2).' ms',
        ];
    }

    $totalTime = microtime(true) - $startTime;

    $recommendations = [];
    if ($totalTime > 0.1) {
        $recommendations[] = 'Total execution time is '.round($totalTime * 1000, 2).'ms. Consider optimizing slow operations.';
    }
    if ($queryCount > 10) {
        $recommendations[] = "High query count ({$queryCount}). Consider using eager loading or caching.";
    }
    if (empty($recommendations)) {
        $recommendations[] = 'Backend performance looks good. Check frontend optimization and network conditions.';
    }

    return response()->json([
        'total_execution_time' => round($totalTime * 1000, 2).' ms',
        'total_queries' => $queryCount,
        'memory_usage' => round(memory_get_usage(true) / 1024 / 1024, 2).' MB',
        'peak_memory' => round(memory_get_peak_usage(true) / 1024 / 1024, 2).' MB',
        'php_version' => PHP_VERSION,
        'laravel_version' => app()->version(),
        'environment' => app()->environment(),
        'tests' => $results,
        'recommendations' => $recommendations,
    ]);
})->name('performance.test');

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

// Debug Route for Permissions

// Messaging & Notifications
Route::middleware(['auth'])->group(function () {
    // Basic messaging - available to all authenticated users
    Route::get('/messages/data/{recipient?}', [MessageController::class, 'getData'])->name('messages.data');
    Route::post('/messages', [MessageController::class, 'store'])->name('messages.store');
    Route::delete('/messages/{message}', [MessageController::class, 'destroy'])->name('messages.destroy');
    Route::post('/messages/{message}/react', [MessageController::class, 'react'])->name('messages.react');
    Route::patch('/messages/{message}', [MessageController::class, 'update'])->name('messages.update');
    Route::post('/messages/typing', [MessageController::class, 'typing'])->name('messages.typing');
    Route::get('/messages/typing/{user}', [MessageController::class, 'typingStatus'])->name('messages.typingStatus');

    // Message export - requires permission
    Route::get('/messages/threads/{user}/export', [MessageController::class, 'exportThreadCsv'])
        ->middleware('custom_permission:export messages')
        ->name('messages.export');

    // Groups - available to all authenticated users
    Route::get('/groups', [GroupController::class, 'index'])->name('groups.index');
    Route::post('/groups', [GroupController::class, 'store'])->name('groups.store');
    Route::get('/groups/{group}/messages', [GroupMessageController::class, 'index'])->name('groups.messages.index');
    Route::post('/groups/{group}/messages', [GroupMessageController::class, 'store'])->name('groups.messages.store');
    Route::post('/groups/{group}/messages/{message}/react', [GroupMessageController::class, 'react'])->name('groups.messages.react');

    // Users for group creation - available to all authenticated users
    Route::get('/users/for-groups', function () {
        $users = \App\Models\User::select('id', 'name', 'email')
            ->where('id', '!=', Auth::id())
            ->orderBy('name')
            ->get();

        return response()->json(['data' => $users]);
    })->name('users.for-groups');

    // Inventory alerts count - available to all authenticated users
    Route::get('/dashboard/inventory-alerts/count', function () {
        try {
            $count = \App\Models\InventoryAlert::count();

            return response()->json(['count' => $count]);
        } catch (\Exception $e) {
            return response()->json(['count' => 0]);
        }
    })->name('inventory-alerts.count.public');

    // Debug route to check authentication
    Route::get('/debug/auth', function () {
        return response()->json([
            'authenticated' => Auth::check(),
            'user_id' => Auth::id(),
            'user_name' => Auth::user()?->name,
            'user_roles' => Auth::user()?->roles?->pluck('name'),
            'user_permissions' => Auth::user()?->permissions,
        ]);
    })->name('debug.auth');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])
        ->name('notifications.markAsRead');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])
        ->name('notifications.markAllRead');
});

// API Docs (Swagger UI)
Route::get('/api-docs', function () {
    return view('swagger');
})->name('api.docs');

Route::get('/api-docs/spec', function () {
    $path = base_path('docs/openapi-v1.yaml');
    abort_unless(file_exists($path), 404);

    return response()->file($path, [
        'Content-Type' => 'application/yaml',
        'Cache-Control' => 'no-cache',
    ]);
})->name('api.docs.spec');

// Admin & Super Admin
Route::middleware(['auth', 'verified', 'role:'.RoleEnum::SUPER_ADMIN->value.'|'.RoleEnum::ADMIN->value.'|'.RoleEnum::CEO->value])
    ->prefix('dashboard')
    ->name('admin.')
    ->group(function () {
        // Dashboard overview data (Super Admin KPIs)
        Route::get('overview-data', [AdminDashboardController::class, 'overviewData'])->name('overview-data');
        Route::get('overview-series', [AdminDashboardController::class, 'overviewSeries'])->name('overview-series');

        // Patients
        Route::middleware('can:view patients')->group(function () {
            Route::get('patients/export', [PatientController::class, 'export'])->name('patients.export');
            Route::get('patients/print-all', [PatientController::class, 'printAll'])->name('patients.printAll');
            Route::get('patients/print-current', [PatientController::class, 'printCurrent'])->name('patients.printCurrent');
            Route::get('patients/{patient}/print', [PatientController::class, 'printSingle'])->name('patients.printSingle');
        });
        Route::resource('patients', PatientController::class)->middleware([
            'can:view patients,patient', // viewAny, view
            'can:create patients', // create, store
            'can:edit patients,patient', // edit, update
            'can:delete patients,patient', // destroy
        ]);

        // Optimized Patient Controller for performance testing
        Route::prefix('optimized')->name('optimized.')->group(function () {
            Route::middleware('can:view patients')->group(function () {
                Route::get('patients/export', [\App\Http\Controllers\Admin\OptimizedPatientController::class, 'export'])->name('patients.export');
                Route::get('patients/print-all', [\App\Http\Controllers\Admin\OptimizedPatientController::class, 'printAll'])->name('patients.printAll');
                Route::get('patients/print-current', [\App\Http\Controllers\Admin\OptimizedPatientController::class, 'printCurrent'])->name('patients.printCurrent');
                Route::get('patients/{patient}/print', [\App\Http\Controllers\Admin\OptimizedPatientController::class, 'printSingle'])->name('patients.printSingle');
                Route::get('patients/quick-search', [\App\Http\Controllers\Admin\OptimizedPatientController::class, 'quickSearch'])->name('patients.quickSearch');
            });
            Route::resource('patients', \App\Http\Controllers\Admin\OptimizedPatientController::class)->middleware([
                'can:view patients,patient',
                'can:create patients',
                'can:edit patients,patient',
                'can:delete patients,patient',
            ]);

            // Optimized Staff Controller
            Route::get('staff/export', [\App\Http\Controllers\Admin\OptimizedStaffController::class, 'export'])->name('staff.export');
            Route::get('staff/print-all', [\App\Http\Controllers\Admin\OptimizedStaffController::class, 'printAll'])->name('staff.printAll');
            Route::get('staff/print-current', [\App\Http\Controllers\Admin\OptimizedStaffController::class, 'printCurrent'])->name('staff.printCurrent');
            Route::get('staff/{staff}/print', [\App\Http\Controllers\Admin\OptimizedStaffController::class, 'printSingle'])->name('staff.printSingle');
            Route::get('staff/quick-search', [\App\Http\Controllers\Admin\OptimizedStaffController::class, 'quickSearch'])->name('staff.quickSearch');
            Route::get('staff/available', [\App\Http\Controllers\Admin\OptimizedStaffController::class, 'availableStaff'])->name('staff.available');
            Route::post('staff/bulk-update', [\App\Http\Controllers\Admin\OptimizedStaffController::class, 'bulkUpdate'])->name('staff.bulkUpdate');
            Route::resource('staff', \App\Http\Controllers\Admin\OptimizedStaffController::class);

            // Optimized Inventory Controller
            Route::get('inventory-items/export', [\App\Http\Controllers\Admin\OptimizedInventoryItemController::class, 'export'])->name('inventory-items.export');
            Route::get('inventory-items/print-all', [\App\Http\Controllers\Admin\OptimizedInventoryItemController::class, 'printAll'])->name('inventory-items.printAll');
            Route::get('inventory-items/print-current', [\App\Http\Controllers\Admin\OptimizedInventoryItemController::class, 'printCurrent'])->name('inventory-items.printCurrent');
            Route::get('inventory-items/{inventory_item}/print', [\App\Http\Controllers\Admin\OptimizedInventoryItemController::class, 'printSingle'])->name('inventory-items.printSingle');
            Route::get('inventory-items/quick-search', [\App\Http\Controllers\Admin\OptimizedInventoryItemController::class, 'quickSearch'])->name('inventory-items.quickSearch');
            Route::get('inventory-items/low-stock', [\App\Http\Controllers\Admin\OptimizedInventoryItemController::class, 'lowStockAlerts'])->name('inventory-items.lowStock');
            Route::get('inventory-items/maintenance-due', [\App\Http\Controllers\Admin\OptimizedInventoryItemController::class, 'maintenanceDue'])->name('inventory-items.maintenanceDue');
            Route::post('inventory-items/bulk-quantities', [\App\Http\Controllers\Admin\OptimizedInventoryItemController::class, 'bulkUpdateQuantities'])->name('inventory-items.bulkQuantities');
            Route::post('inventory-items/{inventory_item}/adjust-stock', [\App\Http\Controllers\Admin\OptimizedInventoryItemController::class, 'adjustStock'])->name('inventory-items.adjustStock');
            Route::get('inventory-items/dashboard-data', [\App\Http\Controllers\Admin\OptimizedInventoryItemController::class, 'dashboardData'])->name('inventory-items.dashboardData');
            Route::resource('inventory-items', \App\Http\Controllers\Admin\OptimizedInventoryItemController::class);
        });

// Admin & Super Admin
Route::middleware(['auth', 'verified']) // Removed role middleware from here
    ->prefix('dashboard')
    ->name('admin.')
    ->group(function () {
        // Dashboard overview data (Super Admin KPIs)
        Route::get('overview-data', [AdminDashboardController::class, 'overviewData'])->name('overview-data');
        Route::get('overview-series', [AdminDashboardController::class, 'overviewSeries'])->name('overview-series');

        // Patients
        Route::middleware('can:view patients')->group(function () {
            Route::get('patients/export', [PatientController::class, 'export'])->name('patients.export');
            Route::get('patients/print-all', [PatientController::class, 'printAll'])->name('patients.printAll');
            Route::get('patients/print-current', [PatientController::class, 'printCurrent'])->name('patients.printCurrent');
            Route::get('patients/{patient}/print', [PatientController::class, 'printSingle'])->name('patients.printSingle');
        });
        Route::resource('patients', PatientController::class)->middleware([
            'can:view patients,patient', // viewAny, view
            'can:create patients', // create, store
            'can:edit patients,patient', // edit, update
            'can:delete patients,patient', // destroy
        ]);

        // Optimized Patient Controller for performance testing
        Route::prefix('optimized')->name('optimized.')->group(function () {
            Route::middleware('can:view patients')->group(function () {
                Route::get('patients/export', [\App\Http\Controllers\Admin\OptimizedPatientController::class, 'export'])->name('patients.export');
                Route::get('patients/print-all', [\App\Http\Controllers\Admin\OptimizedPatientController::class, 'printAll'])->name('patients.printAll');
                Route::get('patients/print-current', [\App\Http\Controllers\Admin\OptimizedPatientController::class, 'printCurrent'])->name('patients.printCurrent');
                Route::get('patients/{patient}/print', [\App\Http\Controllers\Admin\OptimizedPatientController::class, 'printSingle'])->name('patients.printSingle');
                Route::get('patients/quick-search', [\App\Http\Controllers\Admin\OptimizedPatientController::class, 'quickSearch'])->name('patients.quickSearch');
            });
            Route::resource('patients', \App\Http\Controllers\Admin\OptimizedPatientController::class)->middleware([
                'can:view patients,patient',
                'can:create patients',
                'can:edit patients,patient',
                'can:delete patients,patient',
            ]);

            // Optimized Staff Controller
            Route::middleware('can:view staff')->group(function () {
                Route::get('staff/export', [\App\Http\Controllers\Admin\OptimizedStaffController::class, 'export'])->name('staff.export');
                Route::get('staff/print-all', [\App\Http\Controllers\Admin\OptimizedStaffController::class, 'printAll'])->name('staff.printAll');
                Route::get('staff/print-current', [\App\Http\Controllers\Admin\OptimizedStaffController::class, 'printCurrent'])->name('staff.printCurrent');
                Route::get('staff/{staff}/print', [\App\Http\Controllers\Admin\OptimizedStaffController::class, 'printSingle'])->name('staff.printSingle');
                Route::get('staff/quick-search', [\App\Http\Controllers\Admin\OptimizedStaffController::class, 'quickSearch'])->name('staff.quickSearch');
                Route::get('staff/available', [\App\Http\Controllers\Admin\OptimizedStaffController::class, 'availableStaff'])->name('staff.available');
                Route::post('staff/bulk-update', [\App\Http\Controllers\Admin\OptimizedStaffController::class, 'bulkUpdate'])->name('staff.bulkUpdate');
            });
            Route::resource('staff', \App\Http\Controllers\Admin\OptimizedStaffController::class)->middleware([
                'can:view staff,staff',
                'can:create staff',
                'can:edit staff,staff',
                'can:delete staff,staff',
            ]);

            // Optimized Inventory Controller
            Route::middleware('can:view inventory items')->group(function () {
                Route::get('inventory-items/export', [\App\Http\Controllers\Admin\OptimizedInventoryItemController::class, 'export'])->name('inventory-items.export');
                Route::get('inventory-items/print-all', [\App\Http\Controllers\Admin\OptimizedInventoryItemController::class, 'printAll'])->name('inventory-items.printAll');
                Route::get('inventory-items/print-current', [\App\Http\Controllers\Admin\OptimizedInventoryItemController::class, 'printCurrent'])->name('inventory-items.printCurrent');
                Route::get('inventory-items/{inventory_item}/print', [\App\Http\Controllers\Admin\OptimizedInventoryItemController::class, 'printSingle'])->name('inventory-items.printSingle');
                Route::get('inventory-items/quick-search', [\App\Http\Controllers\Admin\OptimizedInventoryItemController::class, 'quickSearch'])->name('inventory-items.quickSearch');
                Route::get('inventory-items/low-stock', [\App\Http\Controllers\Admin\OptimizedInventoryItemController::class, 'lowStockAlerts'])->name('inventory-items.lowStock');
                Route::get('inventory-items/maintenance-due', [\App\Http\Controllers\Admin\OptimizedInventoryItemController::class, 'maintenanceDue'])->name('inventory-items.maintenanceDue');
                Route::post('inventory-items/bulk-quantities', [\App\Http\Controllers\Admin\OptimizedInventoryItemController::class, 'bulkUpdateQuantities'])->name('inventory-items.bulkQuantities');
                Route::post('inventory-items/{inventory_item}/adjust-stock', [\App\Http\Controllers\Admin\OptimizedInventoryItemController::class, 'adjustStock'])->name('inventory-items.adjustStock');
                Route::get('inventory-items/dashboard-data', [\App\Http\Controllers\Admin\OptimizedInventoryItemController::class, 'dashboardData'])->name('inventory-items.dashboardData');
            });
            Route::resource('inventory-items', \App\Http\Controllers\Admin\OptimizedInventoryItemController::class)->middleware([
                'can:view inventory items,inventory_item',
                'can:create inventory items',
                'can:edit inventory items,inventory_item',
                'can:delete inventory items,inventory_item',
            ]);

        // Caregiver Assignments
        Route::middleware('can:view assignments')->group(function () {
            Route::get('assignments/export', [CaregiverAssignmentController::class, 'export'])->name('assignments.export');
            Route::get('assignments/print-all', [CaregiverAssignmentController::class, 'printAll'])->name('assignments.printAll');
            Route::get('assignments/print-current', [CaregiverAssignmentController::class, 'printCurrent'])->name('assignments.printCurrent');
            Route::get('assignments/{assignment}/print', [CaregiverAssignmentController::class, 'printSingle'])->name('assignments.print');
        });
        Route::resource('assignments', CaregiverAssignmentController::class)->middleware([
            'can:view assignments,assignment',
            'can:create assignments',
            'can:edit assignments,assignment',
            'can:delete assignments,assignment',
        ]);

        // Visit Services
        Route::middleware('can:view visit services')->group(function () {
            Route::get('visit-services/export', [VisitServiceController::class, 'export'])->name('visit-services.export');
            Route::get('visit-services/print-all', [VisitServiceController::class, 'printAll'])->name('visit-services.printAll');
            Route::get('visit-services/print-current', [VisitServiceController::class, 'printCurrent'])->name('visit-services.printCurrent');
            Route::get('visit-services/{visit_service}/print', [VisitServiceController::class, 'printSingle'])->name('visit-services.print');
        });
        Route::resource('visit-services', VisitServiceController::class)->middleware([
            'can:view visit services,visit_service',
            'can:create visit services',
            'can:edit visit services,visit_service',
            'can:delete visit services,visit_service',
        ]);

        // Medical Documents
        Route::middleware('can:view medical documents')->group(function () {
            Route::get('medical-documents/export', [MedicalDocumentController::class, 'export'])->name('medical-documents.export');
            Route::get('medical-documents/print-all', [MedicalDocumentController::class, 'printAll'])->name('medical-documents.printAll');
            Route::get('medical-documents/print-current', [MedicalDocumentController::class, 'printCurrent'])->name('medical-documents.printCurrent');
            Route::get('medical-documents/{medical_document}/print', [MedicalDocumentController::class, 'printSingle'])->name('medical-documents.printSingle');
        });
        Route::resource('medical-documents', MedicalDocumentController::class)->middleware([
            'can:view medical documents,medical_document',
            'can:create medical documents',
            'can:edit medical documents,medical_document',
            'can:delete medical documents,medical_document',
        ]);

        // Prescriptions
        Route::middleware('can:view prescriptions')->group(function () {
            Route::get('prescriptions/export', [PrescriptionController::class, 'export'])->name('prescriptions.export');
            Route::get('prescriptions/print-all', [PrescriptionController::class, 'printAll'])->name('prescriptions.printAll');
            Route::get('prescriptions/print-current', [PrescriptionController::class, 'printCurrent'])->name('prescriptions.printCurrent');
            Route::get('prescriptions/{prescription}/print', [PrescriptionController::class, 'printSingle'])->name('prescriptions.printSingle');
        });
        Route::resource('prescriptions', PrescriptionController::class)->middleware([
            'can:view prescriptions,prescription',
            'can:create prescriptions',
            'can:edit prescriptions,prescription',
            'can:delete prescriptions,prescription',
        ]);

        // Medical Records (alias for medical-documents for backward compatibility)
        Route::get('medical-records', [MedicalDocumentController::class, 'index'])->name('medical-records.index')->middleware('can:view medical documents');

        // Appointments (placeholder route - implement AppointmentController if needed)
        Route::get('appointments', function () {
            return redirect()->route('admin.visit-services.index');
        })->name('appointments.index')->middleware('can:view visit services');

        // Inventory Management - Suppliers (trimmed: removed export/import/PDF routes)
        Route::middleware('can:view suppliers')->group(function () {
            Route::get('suppliers/print-all', [App\Http\Controllers\Admin\SupplierController::class, 'printAll'])->name('suppliers.printAll');
            Route::get('suppliers/{supplier}/print', [App\Http\Controllers\Admin\SupplierController::class, 'printSingle'])->name('suppliers.printSingle');
        });
        Route::resource('suppliers', App\Http\Controllers\Admin\SupplierController::class)->middleware([
            'can:view suppliers,supplier',
            'can:create suppliers',
            'can:edit suppliers,supplier',
            'can:delete suppliers,supplier',
        ]);

        Route::middleware('can:view inventory items')->group(function () {
            Route::get('inventory-items/export', [App\Http\Controllers\Admin\InventoryItemController::class, 'export'])->name('inventory-items.export');
            Route::get('inventory-items/print-all', [App\Http\Controllers\Admin\InventoryItemController::class, 'printAll'])->name('inventory-items.printAll');
            Route::get('inventory-items/{inventory_item}/print', [App\Http\Controllers\Admin\InventoryItemController::class, 'printSingle'])->name('inventory-items.printSingle');
        });
        Route::resource('inventory-items', App\Http\Controllers\Admin\InventoryItemController::class)->middleware([
        Route::get('insurance-policies/{insurance_policy}/print', [App\Http\Controllers\Insurance\InsurancePolicyController::class, 'printSingle'])->name('insurance-policies.print');
        Route::resource('insurance-policies', App\Http\Controllers\Insurance\InsurancePolicyController::class);
        Route::get('employee-insurance-records/export', [App\Http\Controllers\Insurance\EmployeeInsuranceRecordController::class, 'export'])->name('employee-insurance-records.export');
        Route::get('employee-insurance-records/print-all', [App\Http\Controllers\Insurance\EmployeeInsuranceRecordController::class, 'printAll'])->name('employee-insurance-records.printAll');
        Route::get('employee-insurance-records/print-current', [App\Http\Controllers\Insurance\EmployeeInsuranceRecordController::class, 'printCurrent'])->name('employee-insurance-records.printCurrent');
        Route::get('employee-insurance-records/{employee_insurance_record}/print', [App\Http\Controllers\Insurance\EmployeeInsuranceRecordController::class, 'printSingle'])->name('employee-insurance-records.print');
        Route::resource('employee-insurance-records', App\Http\Controllers\Insurance\EmployeeInsuranceRecordController::class);
        Route::resource('insurance-claims', App\Http\Controllers\Insurance\InsuranceClaimController::class);
        Route::post('insurance-claims/{insurance_claim}/process-payment', [App\Http\Controllers\Insurance\InsuranceClaimController::class, 'processPayment'])->name('insurance-claims.process-payment');
        Route::post('insurance-claims/{insurance_claim}/update-status', [App\Http\Controllers\Insurance\InsuranceClaimController::class, 'updateStatus'])->name('insurance-claims.update-status');
        Route::get('insurance-claims/{insurance_claim}/print', [App\Http\Controllers\Insurance\InsuranceClaimController::class, 'printSingle'])->name('insurance-claims.print');
        Route::get('insurance-claims/print-current', [App\Http\Controllers\Insurance\InsuranceClaimController::class, 'printCurrent'])->name('insurance-claims.printCurrent');
        Route::post('insurance-claims/{insurance_claim}/send-email', [App\Http\Controllers\Insurance\InsuranceClaimController::class, 'sendClaimEmail'])->name('insurance-claims.send-email');
        // Exchange Rates feature removed
        Route::get('ethiopian-calendar-days/export', [App\Http\Controllers\Insurance\EthiopianCalendarDayController::class, 'export'])->name('ethiopian-calendar-days.export');
        Route::get('ethiopian-calendar-days/print-all', [App\Http\Controllers\Insurance\EthiopianCalendarDayController::class, 'printAll'])->name('ethiopian-calendar-days.printAll');
        Route::get('ethiopian-calendar-days/print-current', [App\Http\Controllers\Insurance\EthiopianCalendarDayController::class, 'printCurrent'])->name('ethiopian-calendar-days.printCurrent');
        Route::get('ethiopian-calendar-days/{ethiopian_calendar_day}/print', [App\Http\Controllers\Insurance\EthiopianCalendarDayController::class, 'printSingle'])->name('ethiopian-calendar-days.print');
        Route::resource('ethiopian-calendar-days', App\Http\Controllers\Insurance\EthiopianCalendarDayController::class);

        // Events
        Route::get('events/export', [EventController::class, 'export'])->name('events.export');
        Route::get('events/print-current', [EventController::class, 'printCurrent'])->name('events.printCurrent');
        Route::get('events/{event}/print', [EventController::class, 'printSingle'])->name('events.print');
        Route::resource('events', EventController::class);

        // Eligibility Criteria (place specific routes BEFORE resource to avoid show route capturing them)
        Route::get('eligibility-criteria/export', [EligibilityCriteriaController::class, 'export'])->name('eligibility-criteria.export');
        Route::get('eligibility-criteria/print-current', [EligibilityCriteriaController::class, 'printCurrent'])->name('eligibility-criteria.printCurrent');
        Route::get('eligibility-criteria/{eligibility_criterion}/print', [EligibilityCriteriaController::class, 'printSingle'])->name('eligibility-criteria.print');
        Route::resource('eligibility-criteria', EligibilityCriteriaController::class);

        // Place specific routes before resource to avoid show route capturing them
        Route::get('event-recommendations/export', [EventRecommendationController::class, 'export'])->name('event-recommendations.export');
        Route::get('event-recommendations/print-current', [EventRecommendationController::class, 'printCurrent'])->name('event-recommendations.printCurrent');
        Route::get('event-recommendations/{event_recommendation}/print', [EventRecommendationController::class, 'printSingle'])->name('event-recommendations.print');
        Route::resource('event-recommendations', EventRecommendationController::class);

        // Event Participants
        Route::resource('event-participants', EventParticipantController::class);
        Route::get('event-participants/export', [EventParticipantController::class, 'export'])->name('event-participants.export');
        Route::get('event-participants/print-all', [EventParticipantController::class, 'printAll'])->name('event-participants.printAll');
        Route::get('event-participants/print-current', [EventParticipantController::class, 'printCurrent'])->name('event-participants.printCurrent');
        Route::get('event-participants/{event_participant}/print', [EventParticipantController::class, 'printSingle'])->name('event-participants.print');

        // Event Staff Assignments
        Route::resource('event-staff-assignments', EventStaffAssignmentController::class);
        Route::get('event-staff-assignments/export', [EventStaffAssignmentController::class, 'export'])->name('event-staff-assignments.export');
        Route::get('event-staff-assignments/print-all', [EventStaffAssignmentController::class, 'printAll'])->name('event-staff-assignments.printAll');
        Route::get('event-staff-assignments/print-current', [EventStaffAssignmentController::class, 'printCurrent'])->name('event-staff-assignments.printCurrent');
        Route::get('event-staff-assignments/{event_staff_assignment}/print', [EventStaffAssignmentController::class, 'printSingle'])->name('event-staff-assignments.print');

        // Event Broadcasts (keep only print-current)
        // Define specific routes before resource to avoid parameter capture
        Route::get('event-broadcasts/print-current', [EventBroadcastController::class, 'printCurrent'])->name('event-broadcasts.printCurrent');
        Route::resource('event-broadcasts', EventBroadcastController::class);

        // Admin Leave Requests
        Route::get('admin-leave-requests', [AdminLeaveRequestController::class, 'index'])->name('leave-requests.index');
        Route::resource('admin-leave-requests', AdminLeaveRequestController::class)
            ->parameters(['admin-leave-requests' => 'leave_request'])
            ->names('leave-requests')
            ->only(['update']);

        // Global Search
        Route::get('global-search', [GlobalSearchController::class, 'search'])->name('global-search');

        // Task Delegations (Admin)
        Route::resource('task-delegations', AdminTaskController::class)
            ->parameters(['task-delegations' => 'task_delegation']);

        // System Management (Super Admin only)
        Route::middleware('role:'.RoleEnum::SUPER_ADMIN->value)->group(function () {
            Route::resource('roles', RoleController::class);
            Route::resource('users', UserController::class);
        });

        // Reports (Admin & Super Admin)
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('service-volume', [\App\Http\Controllers\Reports\ServiceVolumeController::class, 'index'])
                ->name('service-volume');
            Route::get('service-volume/data', [\App\Http\Controllers\Reports\ServiceVolumeController::class, 'data'])
                ->name('service-volume.data');
            Route::get('service-volume/export', [\App\Http\Controllers\Reports\ServiceVolumeController::class, 'export'])
                ->name('service-volume.export');
            Route::get('revenue-ar', [\App\Http\Controllers\Reports\RevenueARController::class, 'index'])
                ->name('revenue-ar');
            Route::get('revenue-ar/data', [\App\Http\Controllers\Reports\RevenueARController::class, 'data'])
                ->name('revenue-ar.data');
            Route::get('revenue-ar/export', [\App\Http\Controllers\Reports\RevenueARController::class, 'export'])
                ->name('revenue-ar.export');
            Route::get('marketing-roi', [\App\Http\Controllers\Reports\MarketingRoiController::class, 'index'])
                ->name('marketing-roi');
            Route::get('marketing-roi/data', [\App\Http\Controllers\Reports\MarketingRoiController::class, 'data'])
                ->name('marketing-roi.data');
            Route::get('marketing-roi/export', [\App\Http\Controllers\Reports\MarketingRoiController::class, 'export'])
                ->name('marketing-roi.export');
        });
    });

// Accountant/Payment Reconciliation
Route::prefix('reconciliation')->name('reconciliation.')->group(function () {
    Route::get('/', [App\Http\Controllers\Accountant\PaymentReconciliationController::class, 'index'])->name('index');
    Route::post('{claimId}/process-payment', [App\Http\Controllers\Accountant\PaymentReconciliationController::class, 'processClaimPayment'])->name('processClaimPayment');
});

// Staff-Specific
Route::middleware(['auth', 'verified', 'role:'.RoleEnum::STAFF->value])
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
require __DIR__.'/auth.php';
require __DIR__.'/settings.php';
require __DIR__.'/marketing.php';

// Test Routes (remove in production)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('test/ui', function () {
        return Inertia::render('Test/UITest');
    })->name('test.ui');

    Route::post('test/flash-message', function () {
        return redirect()->route('test.ui')->with([
            'banner' => 'This is a test toast notification! Your UI implementations are working correctly.',
            'bannerStyle' => 'success',
        ]);
    })->name('test.flash-message');
});

// Cache Performance Test Routes (remove in production)
require __DIR__.'/cache-test.php';

// Performance Comparison Test Routes (remove in production)
require __DIR__.'/performance-test.php';
