# POLICIES — Coverage Summary

This document summarizes the authorization policies and the RBAC permission mapping used across sensitive modules. Super Admin bypass is configured via Gate::before.

Key
- Actions map to granular permissions (via spatie/laravel-permission)
- All policies implement: viewAny, view, create, update, delete (+ restore/forceDelete as aliases)

Patients
- Model: App\Models\Patient → Policy: App\Policies\PatientPolicy
- Permissions: view patients, create patients, edit patients, delete patients

Users
- Model: App\Models\User → Policy: App\Policies\UserPolicy
- Permissions: view users, create users, edit users, delete users

Roles
- Model: Spatie\Permission\Models\Role → Policy: App\Policies\RolePolicy
- Permissions: view roles, create roles, edit roles, delete roles (manage roles also accepted)

Messages
- Model: App\Models\Message → Policy: App\Policies\MessagePolicy (existing)
- Permissions: enforced by MessagePolicy (view/create/edit/delete messages as applicable)

Referrals
- Model: App\Models\Referral → Policy: App\Policies\ReferralPolicy
- Permissions: view referrals, create referrals, edit referrals, delete referrals

Medical Documents
- Model: App\Models\MedicalDocument → Policy: App\Policies\MedicalDocumentPolicy (existing)
- Permissions: view medical documents, create medical documents, edit medical documents, delete medical documents

Invoices & Claims
- Model: App\Models\Invoice → Policy: App\Policies\InvoicePolicy (existing)
- Model: App\Models\InsuranceClaim → Policy: App\Policies\InsuranceClaimPolicy (existing)
- Permissions: as implemented in the existing policies

Inventory
- Model: App\Models\InventoryItem → Policy: App\Policies\InventoryItemPolicy
  - Permissions: view/create/edit/delete inventory items
- Model: App\Models\InventoryRequest → Policy: App\Policies\InventoryRequestPolicy
  - Permissions: view/create/edit/delete inventory requests
- Model: App\Models\InventoryTransaction → Policy: App\Policies\InventoryTransactionPolicy
  - Permissions: view/create/edit/delete inventory transactions
- Model: App\Models\InventoryMaintenanceRecord → Policy: App\Policies\InventoryMaintenanceRecordPolicy
  - Permissions: view/create/edit/delete inventory maintenance records
- Model: App\Models\InventoryAlert → Policy: App\Policies\InventoryAlertPolicy
  - Permissions: view/create/edit/delete inventory alerts
- Model: App\Models\Supplier → Policy: App\Policies\SupplierPolicy
  - Permissions: view/create/edit/delete suppliers

Other existing coverage
- Marketing* modules (budgets, tasks, campaign contents) already registered
- VisitService already registered

Super Admin bypass
- Gate::before in AuthServiceProvider grants all abilities to SUPER_ADMIN role.

Notes
- Frontend guards should use the same granular permissions to show/hide UI actions.
- Policies are registered in app/Providers/AuthServiceProvider.php.

