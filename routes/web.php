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
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\MedicalDocumentController;
use App\Http\Controllers\Admin\PrescriptionController;
use App\Http\Controllers\Admin\LeaveRequestController as AdminLeaveRequestController;
use App\Http\Controllers\Admin\PatientController;
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
Route::get('/', fn () => Inertia::render('Welcome'))->name('home');

// Backward-compatible redirect: /admin -> /dashboard
Route::get('/admin', function () {
    return redirect()->route('dashboard');
})->name('admin.legacy_redirect');

// Public signed invoice PDF (no auth, signed URL required)
Route::get('public/invoices/{invoice}/pdf', [InvoiceController::class, 'publicPdf'])
    ->middleware('signed')
    ->name('invoices.public_pdf');

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
Route::middleware(['auth', 'verified'])->group(function () {
    // Route::get('/messages/data', [MessageController::class, 'getData'])->name('messages.data');
    Route::get('/messages/data/{recipient?}', [MessageController::class, 'getData'])->name('messages.data');
    Route::post('/messages', [MessageController::class, 'store'])->name('messages.store');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])
        ->name('notifications.markAsRead');
});

// Admin & Super Admin
Route::middleware(['auth', 'verified', 'role:'.RoleEnum::SUPER_ADMIN->value.'|'.RoleEnum::ADMIN->value])
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
        Route::get('patients/{patient}/print', [PatientController::class, 'printSingle'])->name('patients.printSingle');

        // Resource route (already correctly defined relative to the group)
        Route::resource('patients', PatientController::class);

        // Caregiver Assignments
        Route::get('assignments/export', [CaregiverAssignmentController::class, 'export'])->name('assignments.export');
        Route::get('assignments/print-all', [CaregiverAssignmentController::class, 'printAll'])->name('assignments.printAll');
        Route::get('assignments/print-current', [CaregiverAssignmentController::class, 'printCurrent'])->name('assignments.printCurrent');
        Route::get('assignments/{assignment}/print', [CaregiverAssignmentController::class, 'printSingle'])->name('assignments.print');
        Route::resource('assignments', CaregiverAssignmentController::class);

        // Visit Services
        Route::resource('visit-services', VisitServiceController::class);

        // Medical Documents
        Route::get('medical-documents/export', [MedicalDocumentController::class, 'export'])->name('medical-documents.export');
        Route::get('medical-documents/print-all', [MedicalDocumentController::class, 'printAll'])->name('medical-documents.printAll');
        Route::get('medical-documents/print-current', [MedicalDocumentController::class, 'printCurrent'])->name('medical-documents.printCurrent');
        Route::get('medical-documents/{medical_document}/print', [MedicalDocumentController::class, 'printSingle'])->name('medical-documents.printSingle');
        Route::resource('medical-documents', MedicalDocumentController::class);

        // Prescriptions
        Route::get('prescriptions/export', [PrescriptionController::class, 'export'])->name('prescriptions.export');
        Route::get('prescriptions/print-all', [PrescriptionController::class, 'printAll'])->name('prescriptions.printAll');
        Route::get('prescriptions/print-current', [PrescriptionController::class, 'printCurrent'])->name('prescriptions.printCurrent');
        Route::get('prescriptions/{prescription}/print', [PrescriptionController::class, 'printSingle'])->name('prescriptions.printSingle');
        Route::resource('prescriptions', PrescriptionController::class);

        // Inventory Management - Suppliers (trimmed: removed export/import/PDF routes)
        Route::get('suppliers/print-all', [App\Http\Controllers\Admin\SupplierController::class, 'printAll'])->name('suppliers.printAll');
        Route::get('suppliers/{supplier}/print', [App\Http\Controllers\Admin\SupplierController::class, 'printSingle'])->name('suppliers.printSingle');
        Route::resource('suppliers', App\Http\Controllers\Admin\SupplierController::class);
        Route::get('inventory-items/export', [App\Http\Controllers\Admin\InventoryItemController::class, 'export'])->name('inventory-items.export');
        Route::get('inventory-items/print-all', [App\Http\Controllers\Admin\InventoryItemController::class, 'printAll'])->name('inventory-items.printAll');
        Route::get('inventory-items/{inventory_item}/print', [App\Http\Controllers\Admin\InventoryItemController::class, 'printSingle'])->name('inventory-items.printSingle');
        Route::resource('inventory-items', App\Http\Controllers\Admin\InventoryItemController::class);

        Route::get('inventory-requests/{inventory_request}/print', [App\Http\Controllers\Admin\InventoryRequestController::class, 'printSingle'])->name('inventory-requests.printSingle');
        Route::resource('inventory-requests', App\Http\Controllers\Admin\InventoryRequestController::class);

        Route::get('inventory-transactions/export', [App\Http\Controllers\Admin\InventoryTransactionController::class, 'export'])->name('inventory-transactions.export');
        Route::get('inventory-transactions/print-all', [App\Http\Controllers\Admin\InventoryTransactionController::class, 'printAll'])->name('inventory-transactions.printAll');

        Route::get('inventory-transactions/{inventory_transaction}/print', [App\Http\Controllers\Admin\InventoryTransactionController::class, 'printSingle'])->name('inventory-transactions.printSingle');
        Route::resource('inventory-transactions', App\Http\Controllers\Admin\InventoryTransactionController::class);

        Route::get('inventory-maintenance-records/export', [App\Http\Controllers\Admin\InventoryMaintenanceRecordController::class, 'export'])->name('inventory-maintenance-records.export');
        Route::get('inventory-maintenance-records/print-all', [App\Http\Controllers\Admin\InventoryMaintenanceRecordController::class, 'printAll'])->name('inventory-maintenance-records.printAll');
        Route::get('inventory-maintenance-records/print-current', [App\Http\Controllers\Admin\InventoryMaintenanceRecordController::class, 'printCurrent'])->name('inventory-maintenance-records.printCurrent');
        Route::get('inventory-maintenance-records/{inventory_maintenance_record}/print', [App\Http\Controllers\Admin\InventoryMaintenanceRecordController::class, 'printSingle'])->name('inventory-maintenance-records.printSingle');
        Route::resource('inventory-maintenance-records', App\Http\Controllers\Admin\InventoryMaintenanceRecordController::class);

        Route::get('inventory-alerts/print-all', [App\Http\Controllers\Admin\InventoryAlertController::class, 'printAll'])->name('inventory-alerts.printAll');
        Route::get('inventory-alerts/print-current', [App\Http\Controllers\Admin\InventoryAlertController::class, 'printCurrent'])->name('inventory-alerts.printCurrent');
        Route::get('inventory-alerts/{inventory_alert}/print', [App\Http\Controllers\Admin\InventoryAlertController::class, 'printSingle'])->name('inventory-alerts.printSingle');
        Route::get('inventory-alerts/count', [App\Http\Controllers\Admin\InventoryAlertController::class, 'count'])->name('inventory-alerts.count');
        Route::resource('inventory-alerts', App\Http\Controllers\Admin\InventoryAlertController::class);

        // Staff
        Route::get('staff/export', [StaffController::class, 'export'])->name('staff.export');
        Route::get('staff/print-all', [StaffController::class, 'printAll'])->name('staff.printAll');
        Route::get('staff/print-current', [StaffController::class, 'printCurrent'])->name('staff.printCurrent');
        Route::get('staff/{staff}/print', [StaffController::class, 'printSingle'])->name('staff.print');
        Route::resource('staff', StaffController::class);

        // Partners
        Route::get('partners/export', [\App\Http\Controllers\Admin\PartnerController::class, 'export'])->name('partners.export');
        Route::get('partners/print-all', [\App\Http\Controllers\Admin\PartnerController::class, 'printAll'])->name('partners.printAll');
        Route::get('partners/print-current', [\App\Http\Controllers\Admin\PartnerController::class, 'printCurrent'])->name('partners.printCurrent');
        Route::get('partners/{partner}/print', [\App\Http\Controllers\Admin\PartnerController::class, 'printSingle'])->name('partners.printSingle');
        Route::resource('partners', \App\Http\Controllers\Admin\PartnerController::class);

        // Partner Agreements
        Route::get('partner-agreements/export', [\App\Http\Controllers\Admin\PartnerAgreementController::class, 'export'])->name('partner-agreements.export');
        Route::get('partner-agreements/print-all', [\App\Http\Controllers\Admin\PartnerAgreementController::class, 'printAll'])->name('partner-agreements.printAll');
        Route::get('partner-agreements/print-current', [\App\Http\Controllers\Admin\PartnerAgreementController::class, 'printCurrent'])->name('partner-agreements.printCurrent');
        Route::get('partner-agreements/{partner_agreement}/print', [\App\Http\Controllers\Admin\PartnerAgreementController::class, 'printSingle'])->name('partner-agreements.printSingle');
        Route::resource('partner-agreements', \App\Http\Controllers\Admin\PartnerAgreementController::class);

        // Referrals
        Route::get('referrals/export', [\App\Http\Controllers\Admin\ReferralController::class, 'export'])->name('referrals.export');
        Route::get('referrals/print-current', [\App\Http\Controllers\Admin\ReferralController::class, 'printCurrent'])->name('referrals.printCurrent');
        Route::get('referrals/{referral}/print', [\App\Http\Controllers\Admin\ReferralController::class, 'printSingle'])->name('referrals.printSingle');
        Route::resource('referrals', \App\Http\Controllers\Admin\ReferralController::class);

        // Partner Commissions
        Route::get('partner-commissions/export', [\App\Http\Controllers\Admin\PartnerCommissionController::class, 'export'])->name('partner-commissions.export');
        Route::get('partner-commissions/print-current', [\App\Http\Controllers\Admin\PartnerCommissionController::class, 'printCurrent'])->name('partner-commissions.printCurrent');
        Route::get('partner-commissions/{partner_commission}/print', [\App\Http\Controllers\Admin\PartnerCommissionController::class, 'printSingle'])->name('partner-commissions.printSingle');
        Route::resource('partner-commissions', \App\Http\Controllers\Admin\PartnerCommissionController::class);

        // Partner Engagements
        Route::get('partner-engagements/export', [\App\Http\Controllers\Admin\PartnerEngagementController::class, 'export'])->name('partner-engagements.export');
        Route::get('partner-engagements/print-all', [\App\Http\Controllers\Admin\PartnerEngagementController::class, 'printAll'])->name('partner-engagements.printAll');
        Route::get('partner-engagements/print-current', [\App\Http\Controllers\Admin\PartnerEngagementController::class, 'printCurrent'])->name('partner-engagements.printCurrent');
        Route::get('partner-engagements/{partner_engagement}/print', [\App\Http\Controllers\Admin\PartnerEngagementController::class, 'printSingle'])->name('partner-engagements.printSingle');
        Route::resource('partner-engagements', \App\Http\Controllers\Admin\PartnerEngagementController::class);

        // Partner Integrations - Referral Documents (place specific routes before resource)
        Route::get('referral-documents/export', [ReferralDocumentController::class, 'export'])->name('referral-documents.export');
        Route::get('referral-documents/print-all', [ReferralDocumentController::class, 'printAll'])->name('referral-documents.printAll');
        Route::get('referral-documents/print-current', [ReferralDocumentController::class, 'printCurrent'])->name('referral-documents.printCurrent');
        Route::get('referral-documents/{referral_document}/print', [ReferralDocumentController::class, 'printSingle'])->name('referral-documents.printSingle');
        Route::resource('referral-documents', ReferralDocumentController::class);
        Route::get('shared-invoices/export', [SharedInvoiceController::class, 'export'])->name('shared-invoices.export');
        Route::get('shared-invoices/print-all', [SharedInvoiceController::class, 'printAll'])->name('shared-invoices.printAll');
        Route::get('shared-invoices/print-current', [SharedInvoiceController::class, 'printCurrent'])->name('shared-invoices.printCurrent');
        Route::get('shared-invoices/{shared_invoice}/print', [SharedInvoiceController::class, 'printSingle'])->name('shared-invoices.printSingle');
        Route::resource('shared-invoices', SharedInvoiceController::class);

        // Staff Availabilities
        Route::get('staff-availabilities/events', [StaffAvailabilityController::class, 'getCalendarEvents'])
            ->name('staff-availabilities.events');
        Route::get('staff-availabilities/available-staff', [StaffAvailabilityController::class, 'availableStaff'])
            ->name('staff-availabilities.availableStaff');
        Route::resource('staff-availabilities', StaffAvailabilityController::class)
            ->only(['index', 'store', 'update', 'destroy']);

        // Staff Payouts
        Route::get('staff-payouts', [StaffPayoutController::class, 'index'])->name('staff-payouts.index');
        Route::post('staff-payouts', [StaffPayoutController::class, 'store'])->name('staff-payouts.store');
        Route::get('staff-payouts/print-all', [StaffPayoutController::class, 'printAll'])->name('staff-payouts.printAll');

        // Invoices
        Route::get('invoices/export', [InvoiceController::class, 'export'])->name('invoices.export');
        Route::get('invoices/print-all', [InvoiceController::class, 'printAll'])->name('invoices.printAll');
        Route::get('invoices/print-current', [InvoiceController::class, 'printCurrent'])->name('invoices.printCurrent');
        Route::get('invoices/{invoice}/print', [InvoiceController::class, 'printSingle'])->name('invoices.print');
        Route::get('invoices/{invoice}/share-link', [InvoiceController::class, 'shareLink'])->name('invoices.shareLink');
        // Incoming Invoices queue
        Route::get('invoices/incoming', [InvoiceController::class, 'incoming'])->name('invoices.incoming');
        Route::post('invoices/generate', [InvoiceController::class, 'generate'])->name('invoices.generate');
        Route::post('invoices/{invoice}/approve', [InvoiceController::class, 'approve'])->name('invoices.approve');
        Route::resource('invoices', InvoiceController::class)
            ->except(['edit', 'update', 'destroy']);

        // Services
        Route::resource('services', ServiceController::class);

        // Insurance
        Route::get('insurance-companies/export', [App\Http\Controllers\Insurance\InsuranceCompanyController::class, 'export'])->name('insurance-companies.export');
        Route::get('insurance-companies/print-all', [App\Http\Controllers\Insurance\InsuranceCompanyController::class, 'printAll'])->name('insurance-companies.printAll');
        Route::get('insurance-companies/print-current', [App\Http\Controllers\Insurance\InsuranceCompanyController::class, 'printCurrent'])->name('insurance-companies.printCurrent');
        Route::get('insurance-companies/{insurance_company}/print', [App\Http\Controllers\Insurance\InsuranceCompanyController::class, 'printSingle'])->name('insurance-companies.print');
        Route::resource('insurance-companies', App\Http\Controllers\Insurance\InsuranceCompanyController::class);
        Route::get('corporate-clients/print-current', [App\Http\Controllers\Insurance\CorporateClientController::class, 'printCurrent'])->name('corporate-clients.printCurrent');
        Route::get('corporate-clients/{corporate_client}/print', [App\Http\Controllers\Insurance\CorporateClientController::class, 'printSingle'])->name('corporate-clients.print');
        Route::resource('corporate-clients', App\Http\Controllers\Insurance\CorporateClientController::class);
        Route::get('insurance-policies/export', [App\Http\Controllers\Insurance\InsurancePolicyController::class, 'export'])->name('insurance-policies.export');
        Route::get('insurance-policies/print-current', [App\Http\Controllers\Insurance\InsurancePolicyController::class, 'printCurrent'])->name('insurance-policies.printCurrent');
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
        Route::get('global-search', [App\Http\Controllers\Admin\GlobalSearchController::class, 'search'])->name('global-search');

        // Task Delegations (Admin)
        Route::resource('task-delegations', AdminTaskController::class)
            ->parameters(['task-delegations' => 'task_delegation']);

        // System Management (Super Admin only)
        Route::middleware('role:'.RoleEnum::SUPER_ADMIN->value)->group(function () {
            Route::resource('roles', RoleController::class);
            Route::resource('users', UserController::class);
        });

        // Accountant/Payment Reconciliation
        Route::prefix('reconciliation')->name('reconciliation.')->group(function () {
            Route::get('/', [App\Http\Controllers\Accountant\PaymentReconciliationController::class, 'index'])->name('index');
            Route::post('{claimId}/process-payment', [App\Http\Controllers\Accountant\PaymentReconciliationController::class, 'processClaimPayment'])->name('processClaimPayment');
        });
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
            'bannerStyle' => 'success'
        ]);
    })->name('test.flash-message');
});
