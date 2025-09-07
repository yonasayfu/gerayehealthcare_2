# Phase 6 — Exports, Print, Reports

> Architecture alignment: one print/export pipeline per module; no per-module view clones; keep export logic in services or centralized trait configs.

Centralize export/print paths and standardize views.

---

- [ ] **ExportableTrait audit** (prio:P1)  
  *Task:* Ensure all modules use centralized export trait. Add missing modules.  
  *Ref:* `/Users/yonassayfu/VSProject/gerayehealthcare/app/Http/Traits/ExportableTrait.php`  
  `/Users/yonassayfu/VSProject/gerayehealthcare/app/Exports/*`

- [ ] **Blade layouts for PDF/Print** (prio:P1)  
  *Task:* Enforce central layouts; delete per-module clones.  
  *Ref:*  
  `/Users/yonassayfu/VSProject/gerayehealthcare/resources/views/pdf-layout.blade.php`  
  `/Users/yonassayfu/VSProject/gerayehealthcare/resources/views/print-layout.blade.php`

- [ ] **AdditionalExportConfigs clarity** (prio:P2)  
  *Task:* Document and standardize config usage.  
  *Ref:* `/Users/yonassayfu/VSProject/gerayehealthcare/app/Http/Config/AdditionalExportConfigs.php`

---

### Clean Architecture Alignment
- Controllers call trait handlers or a single service method; no manual CSV/PDF buildup in controllers.
- Keep module configs co-located and tested.

### OriginalIssue Traceability
- Centralize ExportableTrait usage: item 6
- Consolidate print pages across modules: items 30, 57, 58, 61, 64, 66, 68, 71
- AdditionalExportConfigs standardization: item 18

## Commit & Push
1. `git checkout -b fix/phase-6-exports-print`  
2. Commit: `refactor(exports): centralize trait and layouts; standardize configs`  
3. `git push origin fix/phase-6-exports-print`

**Reminder:** Provide working examples (PDF + CSV) for at least 3 modules in PR attachments.
