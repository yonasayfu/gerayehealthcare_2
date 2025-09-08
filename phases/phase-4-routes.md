# Phase 4 — Routes & Structure

> Architecture alignment: route surface reflects module boundaries; API uses Resources; welcome page optionally guarded. Reference Clean Architecture guide (Phase 3).

Clarify route groups, merge marketing if not justified, and ensure mobile API coverage.

---

- [x] **`routes/web.php` structure** (prio:P1) (area:route)  
  *Task:* Group by domain; consistent prefixes; remove perf/test blocks.  
  *Ref:* `/Users/yonassayfu/VSProject/gerayehealthcare/routes/web.php`
  *Notes:* Current file contains: public routes, mixed messaging, admin dashboard data, and many module resources under `prefix('dashboard')` with `name('admin.')`. Refactor to:
  - Public group: home, api-docs, signed public assets
  - Auth group: messaging/notifications
  - Admin group: `prefix('admin')->name('admin.')` for all admin modules (patients, staff, inventory, events, eligibility, insurance, invoices, etc.) with per-domain subgroups
  - Staff group: `prefix('staff')->name('staff.')` for staff dashboards and tools
  - Remove or strictly guard perf/test routes under `app()->environment('local')` (confirm left-over block and delete if not needed)

- [x] **`routes/api.php` for mobile** (prio:P1) (area:api)  
  *Task:* Ensure endpoints for Visits(geo), Events (participants/recommendations/broadcasts), Messaging, Invoices, Inventory, Auth; protect with Sanctum.  
  *Ref:* `/Users/yonassayfu/VSProject/gerayehealthcare/routes/api.php`

- [x] **Merge `routes/marketing.php` if unnecessary** (prio:P2) (area:route)  
  *Task:* Move into `web.php` under `prefix('admin/marketing')` unless strong separation is needed.  
  *Ref:* `/Users/yonassayfu/VSProject/gerayehealthcare/routes/marketing.php`
  *Notes:* File uses `prefix('admin')` and `prefix('staff')`. When merging, nest under existing admin/staff groups to avoid duplicate prefixes and keep route names consistent (`admin.marketing-*`, `staff.marketing-*`).

- [x] **Route naming convention** (prio:P3) (area:route)  
  *Task:* Keep `staff.task-delegations.index` style; document in `ROUTING.md`.  
  *Ref:* `/Users/yonassayfu/VSProject/gerayehealthcare/routes/*`  
  *Deliverable:* `ROUTING.md` added at repository root.

---

### Clean Architecture Alignment
- Avoid defining heavy closures in routes; use controllers.
- Use policy middleware where appropriate (e.g., can:view patients).
- Rate limiting (RouteServiceProvider) tuned for auth/data routes.

### OriginalIssue Traceability
- web.php cleanliness/structure: item 11
- welcome page UI and exposure strategy: item 12
- test/perf routes removal/guarding: items 13–14 (Phase 0 done; confirm here)
- marketing.php separation rationale: item 15 (merge unless justified)
- mobile API coverage: item 16
- staff route naming conventions: item 108
- rate limiter defaults review: item 90

## Commit & Push
1. `git checkout -b fix/phase-4-routes`  
2. Commit: `chore(routes): cleanup grouping, marketing merge, api coverage`  
3. `git push origin fix/phase-4-routes`

**Reminder:** Attach `php artisan route:list` output to PR for quick verification.
