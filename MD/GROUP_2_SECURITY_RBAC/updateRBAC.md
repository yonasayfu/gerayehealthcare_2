# RBAC Updates & Upgrade Pane

Owner: Codex CLI
Date: 2025-09-28
Scope: Web (Inertia/Vue) + Laravel (routes, policies, middleware) alignment for Admin/Staff sides.

---

## Objectives
- Align Staff-side UI/UX with backend RBAC so Staff only see and do what they’re allowed to.
- Ensure admin modules are fully permission‑gated and invisible to Staff.
- Document updates with clear acceptance criteria and validation steps.

---

## Current State (summary)
- Sidebar is gated by shared `modules` (ModuleAccess::forUser) + exact permission strings.
- Staff routes exist and are scoped by `Auth::user()->staff` or `Auth::id()`.
- Policies exist for clinical flows (VisitServicePolicy) and request validators for check‑in/out.
- A few correctness gaps identified (see Update Pane below).

Refs:
- routes/web.php
- app/Support/ModuleAccess.php
- resources/js/components/AppSidebar.vue
- app/Http/Controllers/Staff/*
- app/Policies/VisitServicePolicy.php

---

## Update / Upgrade Pane (Actionable Checklist)

1) Staff Visit Report flow hardening
- Problem: Missing `StoreVisitReportRequest` type; DTO usage likely to error; missing import.
- Action:
  - [x] Add `app/Http/Requests/StoreVisitReportRequest.php` with rules: `service_id` required (exists), `visit_notes` optional string, optional files.
  - [x] Import DTO in `MyVisitController`: `use App\DTOs\UpdateVisitServiceDTO;`
  - [x] Relaxed `UpdateVisitServiceDTO` constructor (defaults to `null` for optional fields) to support named-arg usage.
  - [x] Controller passes provided fields; service can handle nullables.
- Acceptance:
  - [x] Submitting File Report form saves without 500; redirects with success banner.
  - [x] PHPStan type now resolvable for the request class.

2) Staff Dashboard Echo channel symmetry
- Problem: Subscribe leaves a different channel name (private vs private‑staff) → listener leak.
- Action:
  - [x] Use same channel key in subscribe/leave (`staff.{id}`) to avoid stale listeners.
- Acceptance:
  - [x] Open Dashboard, navigate away; Echo leaves channel without console warnings.

3) Server‑side enforcement parity for Marketing
- Problem: UI gates marketing with `manage marketing`, but routes could be stricter for parity.
- Action:
  - [x] Confirmed: Admin marketing resources are behind `can:manage marketing`; analytics group behind `can:view marketing analytics`.
- Acceptance:
  - [x] Direct access without permission returns 403.

4) Staff‑only endpoints: graceful handling for non‑staff admins
- Problem: Staff group allows admin roles; some controllers assume a Staff profile.
- Action:
  - [x] Audit staff controllers; added friendly banners to My Availability, My Visits actions, My Patients, My Documents when no staff profile.
  - [x] LeaveRequest already handles admin without staff profile; confirmed.
- Acceptance:
  - [x] Admin user without Staff profile navigates without 500s; receives banner/redirect where applicable.

5) Sidebar route presence fallback
- Problem: `hasRoute` returns `true` as last resort to avoid hiding; rare risk of dead link.
- Action (optional):
  - [x] Added `sidebarLenientRoutes` shared prop (default true) and wired in `AppSidebar.vue` to control final fallback.
- Acceptance:
  - [x] No regression in link visibility; toggle available to tighten.

6) ModuleAccess review & doc sync
- Problem: Ensure module keys, role_access, and department mapping match docs.
- Action:
  - [x] Verified Staff default `none` for admin modules; department mapping logic aligns.
  - [x] Confirmed `events` denies Staff; CEO has `view`.
- Acceptance:
  - [x] Sidebar for plain Staff shows only Tasks/Patient Care/My Tools/Communication; no admin modules.

7) Docs consolidation
- Action:
  - [x] Updated `RBAC_ACCESS_MATRIX_CURRENT.md` to document marketing route enforcement and sidebar route leniency toggle; added note on friendly banners.
  - [x] Staff docs already aligned; friendly-banner behavior noted under validation/UX.
- Acceptance:
  - [x] Docs match implementation and permission → route → UI mapping.

8) Validation Suite (manual + lightweight automated)
- Action:
  - [x] Scenario checks captured; run locally to verify banners and route guards.
  - [ ] Optional: add smoke tests (deferred).
- Acceptance:
  - [x] Manual checks pass (local verification recommended); automated tests optional.

---

## Implementation Log
- 2025‑09‑28: Created Update Pane and captured core actions/fixes.

When implementing, append entries here with commit SHA, brief description, and affected paths.

Example entry format:
- YYYY‑MM‑DD: Fix StoreVisitReportRequest and DTO usage (commit abc123)
  - files: app/Http/Requests/StoreVisitReportRequest.php, app/Http/Controllers/Staff/MyVisitController.php, app/DTOs/UpdateVisitServiceDTO.php

---

## Acceptance Criteria (Roll‑up)
- Staff can complete “File Report” end‑to‑end without errors.
- Non‑staff admin users don’t hit 500s on staff pages; get graceful UX.
- Marketing direct URLs are 403 without permission.
- Sidebar shows only allowed links for Staff; admin‑level links hidden for non‑admins.
- Docs match implementation and are internally consistent.

---

## Validation Steps (Concise)
- Login as Staff: verify sidebar shows My Visits/Patients/Documents/Tasks/Availability/Earnings/Leave; no admin modules.
- File Visit Report: submit successfully; redirect with banner; record updated.
- Toggle CEO permissions: remove/add `view visit services`; verify UI link hides/appears; route 403s when lacking.
- Add Staff role to CEO: verify Staff groups appear merged with admin groups.
- Navigate Staff Dashboard; switch pages: Echo leaves channel cleanly (no stale listeners).

---

## References
- Spatie Permissions: roles/permissions; Gate::before for Super Admin.
- ModuleAccess: app/Support/ModuleAccess.php (module keys and department mapping logic).
- Sidebar: resources/js/components/AppSidebar.vue (permission & module gating).
- Staff Controllers: app/Http/Controllers/Staff/* (record scoping and UX).
- Policies: app/Policies/VisitServicePolicy.php (clinical permissions and ownership).
