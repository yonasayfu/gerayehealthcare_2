# Phase 10 — Knowledge Base & Documentation

Write the docs that keep the system understandable and maintainable.

---

- [ ] **ARCHITECTURE.md**  
  *Task:* Describe layers (DTOs, Services, Policies, Events/Listeners), routing conventions, exports/print, storage.  
  *Ref:* root docs

- [ ] **SECURITY.md**  
  *Task:* Role matrix (superadmin, admin, ceo, coo, doctor, caregiver, nurse, staff, guest) and module access.  
  *Ref:* policies in `/Users/yonassayfu/VSProject/gerayehealthcare/app/Policies`

- [ ] **ROUTING.md**  
  *Task:* Name conventions (e.g., `staff.task-delegations.index`) and grouping.  
  *Ref:* `/Users/yonassayfu/VSProject/gerayehealthcare/routes/*`

- [ ] **EXPORTS.md**  
  *Task:* How to use `ExportableTrait` + view layouts.  
  *Ref:*  
    `/Users/yonassayfu/VSProject/gerayehealthcare/app/Http/Traits/ExportableTrait.php`  
    `/Users/yonassayfu/VSProject/gerayehealthcare/resources/views/*layout.blade.php`

- [ ] **SEARCH.md**  
  *Task:* Persisting filters through pagination (server + client patterns).  
  *Ref:* list/index pages

- [ ] **MOBILE_API.md**  
  *Task:* API V1 coverage for Flutter, auth flows, response shapes (Resources).  
  *Ref:* `/Users/yonassayfu/VSProject/gerayehealthcare/app/Http/Controllers/Api/V1/*`

---

## Commit & Push
1. `git checkout -b fix/phase-10-docs`  
2. Commit: `docs: add architecture, security, routing, exports, search, mobile api`  
3. `git push origin fix/phase-10-docs`

**Reminder:** Link each doc from the project `README.md` so newcomers land on the right map.
