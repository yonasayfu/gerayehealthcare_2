# RBAC Access Matrix (Current)

Applies to default configuration. Fine-grained access is controlled via Spatie permissions and Laravel policies; roles can have multiple assignments (e.g., CEO + Staff). Super Admin bypasses all checks (Gate::before).

## Roles

- Super Admin: Full access across all modules.
- CEO: Executive view across modules; can access analytics/reports; operational access is permission-driven.
- COO: Operational lead; access to staff/visits/patients/inventory; analytics/reports; permission-driven.
- Admin: Administrative management across most modules; permission-driven.
- Staff: Self-service (my-*) and limited clinical/operational actions per assigned permissions; staff-only routes under `dashboard/staff.*` also available.
- Guest: No authenticated access.

## Module → Permission Keys

- Patients: `view patients`, `create patients`, `edit patients`, `delete patients`
- Visit Services: `view visit services`, `create visit services`, `edit visit services`, `delete visit services`
- Assignments: `view assignments`, `create assignments`, `edit assignments`, `delete assignments`
- Medical Documents: `view medical documents`, `create medical documents`, `edit medical documents`, `delete medical documents`
- Prescriptions: `view prescriptions`, `create prescriptions`, `edit prescriptions`, `delete prescriptions`
- Staff: `view staff`, `create staff`, `edit staff`, `delete staff`
- Staff Availabilities: `view staff availabilities`, `create staff availabilities`, `edit staff availabilities`, `delete staff availabilities`
- Staff Payouts: `view staff payouts`
- Services Catalog: `view services`
- Inventory Items: `view inventory items`, `create inventory items`, `edit inventory items`, `delete inventory items`
- Inventory Requests: `view inventory requests`, `create inventory requests`, `edit inventory requests`, `delete inventory requests`
- Inventory Transactions: `view inventory transactions`, `create inventory transactions`, `edit inventory transactions`, `delete inventory transactions`
- Inventory Maintenance: `view inventory maintenance records`, `create inventory maintenance records`, `edit inventory maintenance records`, `delete inventory maintenance records`
- Inventory Alerts: `view inventory alerts`, `create inventory alerts`, `edit inventory alerts`, `delete inventory alerts`
- Suppliers: `view suppliers`, `create suppliers`, `edit suppliers`, `delete suppliers`
- Partners & Agreements: `view partners`, `view partner agreements`, `view referrals`, etc.
- Insurance: `view insurance companies`, `view insurance policies`, `view employee insurance records`, `view insurance claims`
- Ethiopian Calendar: `view ethiopian calendar days`
- Events: `view events`, `view eligibility criteria`, `view event recommendations`, `view event staff assignments`, `view event participants`, `view event broadcasts`
- Marketing: `manage marketing` (UI), plus granular: `view marketing campaigns`, `view marketing leads`, `view marketing analytics` (API)
- Reports: `view reports`
- Analytics (admin analytics dashboard): `view analytics dashboard`
- Global Search: `perform global search`
- Messaging (web + API): `view messages` (reads), `export messages` (export)
- Notifications: `view notifications`

## Default Visibility (Sidebar)

- Links render only if the user has the exact permission used by the backend route.
- Staff-only tools (My Tasks, My Visits, My Availability, My Earnings, My Leave Requests) require the Staff role and render independently of admin modules.
- System Management (Roles/Users) renders only for Super Admin by default: `manage roles`, `manage users`.

## API Protection

- API (`/api/v1`) guarded by Sanctum. Endpoints further protected with permission middleware mirroring the above keys (e.g., `permission:view prescriptions`). Rate limits applied on write operations.

## Multi-Role Behavior

- When a user has multiple roles (e.g., CEO + Staff), the sidebar merges Admin and Staff groups, deduplicating overlapping links.
- Super Admin always sees all modules; Staff tools are additionally shown if the Super Admin also has the Staff role.

## Next Steps / Validation

- Verify role/permission assignment UI updates reflect in Inertia `auth.user.permissions` promptly (navigate or reload to refresh sidebar).
- Optionally add server-side `can:` middleware to marketing routes for stricter enforcement.

