# Phase 9 — Cleanup & Deletions

> Architecture alignment: remove dead code, unify shared components, and keep thin-controller patterns intact.

Remove dead code and centralize duplicated components.

---

- [ ] **Delete unused components** (prio:P2)  
  *Task:* Remove components with no references (e.g., `MarketingAnalyticsDashboard.vue` if unused).  
  *Ref:* `/Users/yonassayfu/VSProject/gerayehealthcare/resources/js/components/MarketingAnalyticsDashboard.vue`

- [ ] **Centralize `/components/print`** (prio:P1)  
  *Task:* Use one print UI; remove per-module `printCurrent.vue` / `printAll.vue`.  
  *Ref:* `/Users/yonassayfu/VSProject/gerayehealthcare/resources/js/components/print/*`

- [ ] **Remove orphan views** (prio:P2)  
  *Task:* Delete views not used (e.g., `Insurance/Policies/PrintCurrent.vue`).  
  *Ref:* `/Users/yonassayfu/VSProject/gerayehealthcare/resources/js/pages/Insurance/Policies/PrintCurrent.vue`

---

### Clean Architecture Alignment
- Prefer central components/composables over copy/paste; keep export/print central.

### OriginalIssue Traceability
- Delete unused components and prints: items 30, 56–58, 79
- Prune unused patient and supplier fragments: items 39, 48

## Commit & Push
1. `git checkout -b fix/phase-9-cleanup`  
2. Commit: `chore(cleanup): remove unused components and centralize print`  
3. `git push origin fix/phase-9-cleanup`

**Reminder:** Add a short list of removed files to the PR body for traceability.
