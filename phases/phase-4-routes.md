# Phase 4 — Routes & Structure

Clarify route groups, merge marketing if not justified, and ensure mobile API coverage.

---

- [ ] **`routes/web.php` structure** (prio:P1) (area:route)  
  *Task:* Group by domain; consistent prefixes; remove perf/test blocks.  
  *Ref:* `/Users/yonassayfu/VSProject/gerayehealthcare/routes/web.php`

- [ ] **`routes/api.php` for mobile** (prio:P1) (area:api)  
  *Task:* Ensure endpoints for Visits(geo), Events (participants/recommendations/broadcasts), Messaging, Invoices, Inventory, Auth; protect with Sanctum.  
  *Ref:* `/Users/yonassayfu/VSProject/gerayehealthcare/routes/api.php`

- [ ] **Merge `routes/marketing.php` if unnecessary** (prio:P2) (area:route)  
  *Task:* Move into `web.php` under `prefix('admin/marketing')` unless strong separation is needed.  
  *Ref:* `/Users/yonassayfu/VSProject/gerayehealthcare/routes/marketing.php`

- [ ] **Route naming convention** (prio:P3) (area:route)  
  *Task:* Keep `staff.task-delegations.index` style; document in `ROUTING.md`.  
  *Ref:* `/Users/yonassayfu/VSProject/gerayehealthcare/routes/*`

---

## Commit & Push
1. `git checkout -b fix/phase-4-routes`  
2. Commit: `chore(routes): cleanup grouping, marketing merge, api coverage`  
3. `git push origin fix/phase-4-routes`

**Reminder:** Attach `php artisan route:list` output to PR for quick verification.
