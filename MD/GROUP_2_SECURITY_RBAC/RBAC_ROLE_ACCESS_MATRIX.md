# RBAC Role Access Matrix

This guide pairs each Geraye Healthcare role with the application modules it can see or manage. Use it as the single source of truth when onboarding staff, answering "why am I seeing a 403?" questions, or planning new permissions.

> **Legend**
> - **Full** – create/edit/delete plus exports where applicable
> - **Manage** – create/edit (no destructive operations)
> - **View** – read-only (lists, details, dashboards)
> - **—** – no access (menu items hidden)

## Roles at a glance

| Role | Mission | Typical Users |
| --- | --- | --- |
| Super Admin | Owns the platform. Automatically bypasses all checks. | Founders, Platform Owners |
| CEO | Executive visibility, reporting, approvals. | CEO, Board Members |
| COO | Day-to-day operations lead. Manages staff and services. | COO, Operations Director |
| Admin | Administrative power users handling most back-office workflows. | Operations Admin, Finance Admin |
| Staff | Front-line caregivers and coordinators. | Nurses, Field Staff |

## Module Definitions (with core permissions)

- **Dashboards & Analytics** – `view analytics dashboard`, `view system reports`, `view reports`, `export system reports`
- **Patient Management** – `view/create/edit/delete/export patients`, caregiver assignments
- **Staff Management** – `view/create/edit/delete staff`, `manage staff schedules`, `staff availabilities`, `leave requests`
- **Tasking & Assignments** – assignments and task delegations (`view/create/edit/delete assignments`, `task delegations`)
- **Clinical Records** – medical records, visit services, prescriptions, lab results
- **Medical Documents** – upload/edit/delete patient documents
- **Inventory & Suppliers** – items, requests, transactions, alerts, maintenance, suppliers
- **Service Catalog** – administrative services list (`view services`)
- **Insurance Suite** – insurance companies, corporate clients, policies, claims, employee records, Ethiopian calendar days
- **Partnerships & Referrals** – partners, agreements, engagements, commissions, referrals, referral docs, shared invoices
- **Marketing** – campaigns, leads, budgets, content, marketing tasks, analytics
- **Events** – events, eligibility, recommendations, participants, broadcasts, staff assignments
- **Financial** – invoices, staff payouts, budgets, financial analytics, reconciliation
- **Reports & Global Search** – consolidated reports hub and global search (`perform global search`)
- **Communication** – in-app messaging & notifications
- **System Administration** – core platform settings, logs, maintenance (excludes infrastructure-only ops)

## Role vs Module Access

| Module | Super Admin | CEO | COO | Admin | Staff |
| --- | --- | --- | --- | --- | --- |
| Dashboards & Analytics | Full | View | Full | Full | View (personal KPIs) |
| Patient Management | Full | View | Full | Full | View/Edit own scope |
| Staff Management | Full | View (incl. leave approval) | Full | Manage | — |
| Tasking & Assignments | Full | Manage | Full | Full | View own |
| Clinical Records | Full | View | Full | Full | Manage (their patients) |
| Medical Documents | Full | View | Full | Full | Manage (upload/edit own) |
| Inventory & Suppliers | Full | View | Full | Full | View limited |
| Service Catalog | Full | View | Manage | Manage | — |
| Insurance Suite | Full | View | Manage | Manage | — |
| Partnerships & Referrals | Full | View | Manage | Manage | — |
| Marketing | Full | View Analytics | Insights | Full | — |
| Events | Full | View | Full | Full | View |
| Financial | Full | View | Manage | Full | View (earnings) |
| Reports & Global Search | Full | View | Full | Full | — |
| Communication | Full | Full | Full | Full | Full |
| System Administration | Full | View (health/logs) | Manage core | Manage core | — |

**Notes**
- CEO gains read-only visibility across operational data plus approval ability for leave requests.
- COO and Admin roles now share the operational modules; COO emphasises service delivery, Admin covers back-office, finance, and marketing. Both can operate without 403s after the permission sync.
- Staff are restricted to the information they need for day-to-day work plus personal leave/task flows.

## Keeping navigation and permissions in sync

- **Navigation filtering** comes from `resources/js/components/AppSidebar.vue`. Every menu item now declares the permission it requires, preventing links that lead to 403 errors.
- **Authoritative permission list** lives in `database/seeders/RolesAndPermissionsSeeder.php`. The seeder now:
  - Defines every permission string referenced in routes, policies, or frontend checks.
  - Grants coherent bundles of permissions to each core role.
- **Mobile/API parity**
  - `POST /api/v1/login` now returns the authenticated user (roles, permissions, module list) alongside a Sanctum token scoped to that user’s permissions.
  - `GET /api/v1/modules` returns the same module matrix used by the web app so native clients can build role-aware navigation.
  - Key API routes are protected with the same permission strings used on the web, guaranteeing identical 403 behaviour across platforms.
- **Refresh flow** after changing permissions:
  1. `php artisan permission:cache-reset`
  2. `php artisan db:seed --class=RolesAndPermissionsSeeder`
  3. Ask users to log out/in (role cache is shared via Inertia props).

## Troubleshooting 403s

1. **Confirm the user’s role(s):** `php artisan tinker` → `User::find(ID)->getRoleNames()`.
2. **Check permission presence:** `User::find(ID)->can('permission-name')`.
3. **Verify navigation:** If a menu item is visible but `can()` returns false, ensure `AppSidebar.vue` lists the correct `permission` string.
4. **Policies:** For legacy policies that still gate by role, confirm they align with the permission strategy. (See `app/Policies/*`.)
5. **Cache busting:** Run `php artisan optimize:clear` after altering seeder/policy logic in production.

## Extending RBAC

1. Add your new permission to the seeder under the relevant module grouping.
2. Decide which roles inherit it (append to the respective `givePermissionTo` block).
3. Reference the same permission string in routes (`can:`), Vue views (`can('permission')`), and navigation (`permission:` keys).
4. Re-run the refresh flow above.

With this matrix and the synchronized seeder/UI updates, each role lands on the modules they are supposed to manage—no more guessing or unexpected 403s.
