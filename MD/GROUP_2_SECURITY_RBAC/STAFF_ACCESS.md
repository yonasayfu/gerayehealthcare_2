# Staff Access (Geraye Home Care)

Goal: Ensure Staff users only see and access staff-specific tools. No admin modules should be visible, and no admin URLs should be reachable from the sidebar.

## Staff Can Access

- Tasks
  - My Tasks (`staff.task-delegations.*`)
  - Daily Tasks (`staff.daily-tasks.*`)
  - To‑Do (`staff.my-todo.index`)
- Patient Care
  - My Visits (`staff.my-visits.*`)
  - Check-in/Check-out, File Visit Report
- My Tools
  - My Availability (`staff.my-availability.*`)
  - My Earnings (`staff.my-earnings.*`)
  - Request Leave (`staff.leave-requests.*`) with types (Annual/Sick/Unpaid)
- Communication
  - Messages inbox, group chats, notifications

## Staff Cannot Access (hidden in sidebar)

- Admin modules (Patients, Staff management, Medical Records, Inventory, Marketing, Events, Financial, Insurance, Partnerships, Reports & Analytics, System Admin)
- Any `admin.*` route is hidden for Staff and 403-protected by backend.

## How It Works

- Backend computes module access (ModuleAccess::forUser) and shares it to the frontend; Staff gets `none` for admin modules.
- Sidebar first gates groups by module access, then filters each link by Spatie permission.
- Additionally, any `admin.*` link is hidden for non admin-level roles.

### Sub‑roles (smart staff access)

- Without creating global roles like `doctor`/`nurse`, the backend maps Staff.department/position to module access:
  - Clinical positions (Doctor/Nurse/Caregiver or departments containing Clinic/Care):
    - clinical: manage, medical-documents: manage, patients: view
  - Finance department/position: financial: manage
  - HR department/position: staff-ops: manage
  - IT department/position: system: manage
- This means two Staff users can have different module visibility based on their Staff record — while still only seeing their own data.

## Validation

- Login as `staff@gerayehealthcare.com` (password: `password`).
  - Sidebar shows: Tasks, Patient Care (My Visits), My Tools, Communication.
  - No links to Patients/Medical Records/Inventory/etc.
  - All visible links resolve without 403.
- Grant/revoke a permission for Staff and navigate; the sidebar updates accordingly on next navigation.
 - If an admin user without a Staff profile opens a staff-only page (e.g., My Availability, My Patients, My Documents), they receive a friendly banner and redirect back to dashboard.

## Data Isolation (Staff1 vs Staff2)

- Seed two distinct staff users and data:
  - `php artisan db:seed --class=StaffIsolationDemoSeeder`
  - Creates `staff1@gerayehealthcare.com` and `staff2@gerayehealthcare.com`, each with:
    - Their own Staff record
    - Their own Personal Tasks (To‑Do)
    - Their own upcoming Visits
    - Their own Leave Request
- Verification:
  - Login as Staff One; My Visits, To‑Do, Leave Requests show only Staff One’s records.
  - Login as Staff Two; views show only Staff Two’s records.
  - Neither can see or act on the other’s data from the UI.

Notes:
- Staff controllers filter by `Auth::id()` or `Auth::user()->staff->id` (e.g., PersonalTaskController uses `user_id`; MyVisitController uses `staff_id`; LeaveRequestController uses `staff_id` for staff users).
- Admin roles retain broader access via policies and route middleware; the staff sidebar hides admin routes to avoid 403s.

---

## What “Staff” Means (for presentation)

- Staff is the operational actor that delivers care or internal work: Doctors, Nurses, Caregivers, Physiotherapists, Finance staff, HR, IT.
- A Staff account is a User with a Staff profile (`users.id` 1–1 `staff.user_id`).
- Access is the combination of:
  1) Role (Spatie) → base permissions
  2) Department/Position on Staff → module visibility (smart mapping)
  3) Record‑level scoping → staff sees only data where `user_id` or `staff_id` equals self

## Capabilities Overview (scope)

- Care delivery (clinical staff)
  - See own upcoming visits; check‑in/out; file reports
  - See My Patients (only those they treated)
  - See My Documents (documents they authored)
- Productivity
  - My To‑Do with subtasks, reminders, recurrence, priorities
  - My Tasks (delegations assigned to me)
  - Daily Task Tracking (personal updates; supervisors get KPI dashboard)
- Operations
  - My Availability calendar with conflict checks; timezone‑safe
  - Request Leave (Annual/Sick/Unpaid) with admin approval
- Communication
  - Messages (DMs), groups, notifications

## Route & Permission Map (Staff)

- My Visits: `staff.my-visits.*` (scoped by `staff_id`)
- My Patients: `staff.my-patients.index` (derived from VisitService)
- My Documents: `staff.my-documents.index` (`created_by_staff_id`)
- My To‑Do: `staff.my-todo.*` (scoped by `user_id`)
- My Tasks (delegations): `staff.task-delegations.*` (assigned_to)
- My Availability: `staff.my-availability.*` (scoped by `staff_id`)
- Leave Requests: `staff.leave-requests.*` (scoped by `staff_id` for staff)
- Messages/Notifications: web + API are permission‑gated; all staff can view own inbox

## Realtime Behavior (Staff)

- New caregiver assignment → Staff dashboard auto‑refreshes KPIs & Upcoming Visits
  - Channel: `private-staff.{staff_id}`
  - Event: `caregiver.assignment.created`
- Sidebar badges (my tasks / my to‑do) refresh every 60s (light polling)

## Availability & Timezone Model (saves what you select)

- UI sends local `start_time`/`end_time` + browser timezone (e.g., `Africa/Addis_Ababa`).
- Backend converts to UTC for storage and conflict checks.
- Calendar returns ISO8601 timestamps with offset or UTC ISO strings and renders in local time (`timeZone: 'local'`).
- Result: The time you pick is the time you see — no +/− 3h drift.

## Admin/HQ Visibility of Leaves

- New leave request → Notifies Super Admin, Admin, CEO, COO.
- Admin leave page shows all requests; Staff page shows own.
- Status updates notify the requester with a deep link.

## Testing — Step by Step

1) Fresh seed:
   - `php artisan migrate:fresh --seed`
   - Users: `staff1@…`, `staff2@…` (pwd: `password`), plus HR/Admin test users
2) Login as Staff One → create a To‑Do, Availability, and a Leave Request
3) Switch to Staff Two → verify you cannot see Staff One’s To‑Do/Visits/Leaves
4) Login as HR/Admin → approve Staff One’s leave; requester sees notification
5) Create a caregiver assignment for Staff One → Staff One dashboard updates live
6) Availability: set 06:30–07:00 → verify the slot shows exactly 06:30–07:00

## Demo Script (5–7 min)

1) Login as Staff (Nurse); show My Visits, My Patients, My Documents
2) Create a To‑Do and Availability; show timezone label and no drift
3) Request Leave (Sick); switch to HR → approve; switch back to Staff → bell shows status
4) Create a caregiver assignment as Admin → switch back to Staff; dashboard updates
5) Emphasize staff cannot see other staff data (switch users quickly)

## Troubleshooting

- Sidebar shows admin modules → ensure `ModuleAccess::forUser` shares modules and staff role has no admin module access; re‑login or navigate to refresh Inertia props.
- Availability time jumps → ensure browser timezone is correct and hard‑refresh; calendar uses `timeZone: 'local'` and backend returns ISO strings with offsets.
- No notifications → verify queue/mail are configured; for demo, database notifications suffice.

## Maintenance Notes

- Department mapping logic lives in `App\\Support\\ModuleAccess::resolveByDepartment`.
- Staff isolation guaranteed by controllers: use `Auth::id()` and `Auth::user()->staff->id` filters.
- Seeders for demos: `TestUsersSeeder`, `StaffIsolationDemoSeeder`.
