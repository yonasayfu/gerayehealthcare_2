# RBAC Visibility & Access Plan

Owner: Codex CLI
Date: 2025-09-25

Goal: Ensure users only see and access what their roles/permissions allow. Align web and API routes with RBAC, fix sidebar/nav visibility, and document the access matrix.

## Tasks

- [x] Review project RBAC docs and routes (web/api)
- [x] Align `routes/web.php` admin group with admin-level roles
- [x] Standardize route middleware usage (`permission:` vs custom)
- [x] Audit AppSidebar items vs backend permission strings
- [x] Fix AppSidebar mismatched/missing permission gates
- [x] Merge staff navigation for multi‑role users (e.g., CEO+Staff)
- [x] Clean `NavUser.vue` duplication and stabilize user injection
- [ ] Validate role/permission management UI end-to-end
- [ ] Add a lightweight frontend permission utility (optional)
- [ ] Finalize and publish RBAC Access Matrix (this repo)

## Notes

- Admin routes are now grouped behind roles: super-admin, admin, ceo, coo. Each module still has granular `can:`/`permission:` middleware.
- Sidebar shows links only if the user has the exact permission used by the corresponding route. This prevents 403s for CEO/COO.
- Multi-role users (e.g., Super Admin + Staff) see both admin and staff tools (deduplicated per group).

## Validation Steps

1. Sign in as CEO without “view visit services” permission
   - Expect: “Visit Services” hidden from sidebar; direct URL returns 403.
2. Grant CEO “view visit services” permission
   - Expect: Sidebar now shows “Visit Services”; route loads successfully.
3. Add Staff role to CEO
   - Expect: Staff groups (My Tools/Patient Care) appear alongside Admin groups.
4. Validate Reports
   - With “view reports”: Service Volume, Revenue & AR, Marketing ROI visible.
   - Without: hidden.

## Open Questions

- Marketing resources currently lack explicit route `can:` middleware. If desired, add `can:view/manage marketing` to controllers to enforce server-side too (UI is already conservative).
- Should “Dashboard” be shown only when the user has at least one visible module, or always? Currently: always.

