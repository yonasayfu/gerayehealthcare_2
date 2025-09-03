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
            'can:view inventory items,inventory_item',
            'can:create inventory items',
            'can:edit inventory items,inventory_item',
            'can:delete inventory items,inventory_item',
        ]);

        Route::middleware('can:view inventory requests')->group(function () {
            Route::get('inventory-requests/{inventory_request}/print', [App\Http\Controllers\Admin\InventoryRequestController::class, 'printSingle'])->name('inventory-requests.printSingle');
        });
        Route::resource('inventory-requests', App\Http\Controllers\Admin\InventoryRequestController::class)->middleware([
            'can:view inventory requests,inventory_request',
            'can:create inventory requests',
            'can:edit inventory requests,inventory_request',
            'can:delete inventory requests,inventory_request',
        ]);

        Route::middleware('can:view inventory transactions')->group(function () {
            Route::get('inventory-transactions/export', [App\Http\Controllers\Admin\InventoryTransactionController::class, 'export'])->name('inventory-transactions.export');
            Route::get('inventory-transactions/print-all', [App\Http\Controllers\Admin\InventoryTransactionController::class, 'printAll'])->name('inventory-transactions.printAll');
            Route::get('inventory-transactions/{inventory_transaction}/print', [App\Http\Controllers\Admin\InventoryTransactionController::class, 'printSingle'])->name('inventory-transactions.printSingle');
        });
        Route::resource('inventory-transactions', App\Http\Controllers\Admin\InventoryTransactionController::class)->middleware([
            'can:view inventory transactions,inventory_transaction',
            'can:create inventory transactions',
            'can:edit inventory transactions,inventory_transaction',
            'can:delete inventory transactions,inventory_transaction',
        ]);

        Route::middleware('can:view inventory maintenance records')->group(function () {
            Route::get('inventory-maintenance-records/export', [App\Http\Controllers\Admin\InventoryMaintenanceRecordController::class, 'export'])->name('inventory-maintenance-records.export');
            Route::get('inventory-maintenance-records/print-all', [App\Http\Controllers\Admin\InventoryMaintenanceRecordController::class, 'printAll'])->name('inventory-maintenance-records.printAll');
            Route::get('inventory-maintenance-records/print-current', [App\Http\Controllers\Admin\InventoryMaintenanceRecordController::class, 'printCurrent'])->name('inventory-maintenance-records.printCurrent');
            Route::get('inventory-maintenance-records/{inventory_maintenance_record}/print', [App\Http\Controllers\Admin\InventoryMaintenanceRecordController::class, 'printSingle'])->name('inventory-maintenance-records.printSingle');
        });
        Route::resource('inventory-maintenance-records', App\Http\Controllers\Admin\InventoryMaintenanceRecordController::class)->middleware([
            'can:view inventory maintenance records,inventory_maintenance_record',
            'can:create inventory maintenance records',
            'can:edit inventory maintenance records,inventory_maintenance_record',
            'can:delete inventory maintenance records,inventory_maintenance_record',
        ]);

        Route::middleware('can:view inventory alerts')->group(function () {
            Route::get('inventory-alerts/print-all', [App\Http\Controllers\Admin\InventoryAlertController::class, 'printAll'])->name('inventory-alerts.printAll');
            Route::get('inventory-alerts/print-current', [App\Http\Controllers\Admin\InventoryAlertController::class, 'printCurrent'])->name('inventory-alerts.printCurrent');
            Route::get('inventory-alerts/{inventory_alert}/print', [App\Http\Controllers\Admin\InventoryAlertController::class, 'printSingle'])->name('inventory-alerts.printSingle');
            Route::get('inventory-alerts/count', [App\Http\Controllers\Admin\InventoryAlertController::class, 'count'])->name('inventory-alerts.count');
        });
        Route::resource('inventory-alerts', App\Http\Controllers\Admin\InventoryAlertController::class)->middleware([
            'can:view inventory alerts,inventory_alert',
            'can:create inventory alerts',
            'can:edit inventory alerts,inventory_alert',
            'can:delete inventory alerts,inventory_alert',
        ]);

        // Staff
        Route::middleware('can:view staff')->group(function () {
            Route::get('staff/export', [StaffController::class, 'export'])->name('staff.export');
            Route::get('staff/print-all', [StaffController::class, 'printAll'])->name('staff.printAll');
            Route::get('staff/print-current', [StaffController::class, 'printCurrent'])->name('staff.printCurrent');
            Route::get('staff/{staff}/print', [StaffController::class, 'printSingle'])->name('staff.print');
        });
        Route::resource('staff', StaffController::class)->middleware([
            'can:view staff,staff',
            'can:create staff',
            'can:edit staff,staff',
            'can:delete staff,staff',
        ]);

        // Partners
        Route::middleware('can:view partners')->group(function () {
            Route::get('partners/export', [\App\Http\Controllers\Admin\PartnerController::class, 'export'])->name('partners.export');
            Route::get('partners/print-all', [\App\Http\Controllers\Admin\PartnerController::class, 'printAll'])->name('partners.printAll');
            Route::get('partners/print-current', [\App\Http\Controllers\Admin\PartnerController::class, 'printCurrent'])->name('partners.printCurrent');
            Route::get('partners/{partner}/print', [\App\Http\Controllers\Admin\PartnerController::class, 'printSingle'])->name('partners.printSingle');
        });
        Route::resource('partners', \App\Http\Controllers\Admin\PartnerController::class)->middleware([
            'can:view partners,partner',
            'can:create partners',
            'can:edit partners,partner',
            'can:delete partners,partner',
        ]);

        // Partner Agreements
        Route::middleware('can:view partner agreements')->group(function () {
            Route::get('partner-agreements/export', [\App\Http\Controllers\Admin\PartnerAgreementController::class, 'export'])->name('partner-agreements.export');
            Route::get('partner-agreements/print-all', [\App\Http\Controllers\Admin\PartnerAgreementController::class, 'printAll'])->name('partner-agreements.printAll');
            Route::get('partner-agreements/print-current', [\App\Http\Controllers\Admin\PartnerAgreementController::class, 'printCurrent'])->name('partner-agreements.printCurrent');
            Route::get('partner-agreements/{partner_agreement}/print', [\App\Http\Controllers\Admin\PartnerAgreementController::class, 'printSingle'])->name('partner-agreements.printSingle');
        });
        Route::resource('partner-agreements', \App\Http\Controllers\Admin\PartnerAgreementController::class)->middleware([
            'can:view partner agreements,partner_agreement',
            'can:create partner agreements',
            'can:edit partner agreements,partner_agreement',
            'can:delete partner agreements,partner_agreement',
        ]);

        // Referrals
        Route::middleware('can:view referrals')->group(function () {
            Route::get('referrals/export', [\App\Http\Controllers\Admin\ReferralController::class, 'export'])->name('referrals.export');
            Route::get('referrals/print-current', [\App\Http\Controllers\Admin\ReferralController::class, 'printCurrent'])->name('referrals.printCurrent');
            Route::get('referrals/{referral}/print', [\App\Http\Controllers\Admin\ReferralController::class, 'printSingle'])->name('referrals.printSingle');
        });
        Route::resource('referrals', \App\Http\Controllers\Admin\ReferralController::class)->middleware([
            'can:view referrals,referral',
            'can:create referrals',
            'can:edit referrals,referral',
            'can:delete referrals,referral',
        ]);

        // Partner Commissions
        Route::middleware('can:view partner commissions')->group(function () {
            Route::get('partner-commissions/export', [\App\Http\Controllers\Admin\PartnerCommissionController::class, 'export'])->name('partner-commissions.export');
            Route::get('partner-commissions/print-current', [\App\Http\Controllers\Admin\PartnerCommissionController::class, 'printCurrent'])->name('partner-commissions.printCurrent');
            Route::get('partner-commissions/{partner_commission}/print', [\App\Http\Controllers\Admin\PartnerCommissionController::class, 'printSingle'])->name('partner-commissions.printSingle');
        });
        Route::resource('partner-commissions', \App\Http\Controllers\Admin\PartnerCommissionController::class)->middleware([
            'can:view partner commissions,partner_commission',
            'can:create partner commissions',
            'can:edit partner commissions,partner_commission',
            'can:delete partner commissions,partner_commission',
        ]);

        // Partner Engagements
        Route::middleware('can:view partner engagements')->group(function () {
            Route::get('partner-engagements/export', [\App\Http\Controllers\Admin\PartnerEngagementController::class, 'export'])->name('partner-engagements.export');
            Route::get('partner-engagements/print-all', [\App\Http\Controllers\Admin\PartnerEngagementController::class, 'printAll'])->name('partner-engagements.printAll');
            Route::get('partner-engagements/print-current', [\App\Http\Controllers\Admin\PartnerEngagementController::class, 'printCurrent'])->name('partner-engagements.printCurrent');
            Route::get('partner-engagements/{partner_engagement}/print', [\App\Http\Controllers\Admin\PartnerEngagementController::class, 'printSingle'])->name('partner-engagements.printSingle');
        });
        Route::resource('partner-engagements', \App\Http\Controllers\Admin\PartnerEngagementController::class)->middleware([
            'can:view partner engagements,partner_engagement',
            'can:create partner engagements',
            'can:edit partner engagements,partner_engagement',
            'can:delete partner engagements,partner_engagement',
        ]);

        // Partner Integrations - Referral Documents (place specific routes before resource)
        Route::middleware('can:view referral documents')->group(function () {
            Route::get('referral-documents/export', [ReferralDocumentController::class, 'export'])->name('referral-documents.export');
            Route::get('referral-documents/print-all', [ReferralDocumentController::class, 'printAll'])->name('referral-documents.printAll');
            Route::get('referral-documents/print-current', [ReferralDocumentController::class, 'printCurrent'])->name('referral-documents.printCurrent');
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
