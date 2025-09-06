# Phase 2 — Models, Policies & Morphs

Align models, morph maps, and authorization policies with the real data model.

---

- [x] **Polymorphic map completeness** (prio:P1) (area:model) (type:refactor)  
  *Task:* Expand `Relation::morphMap`.  
  *Why:* Currently includes only `staff` and `patient`; other morphables (e.g., messages, documents, invoices) must be resolvable.  
  *Definition of Done (DoD):*  
    - All morphable entities added; unit tests for morph resolution.  
  *Ref:* `/Users/yonassayfu/VSProject/gerayehealthcare/app/Providers/AppServiceProvider.php`

- [x] **Policies coverage & summary doc** (prio:P1) (area:model|policy) (type:docs)  
  *Task:* Ensure policies exist for sensitive modules and register them.  
  *Why:* Enforces least-privilege (esp. Patients, Invoices, Users, Roles, Messages, Referrals, MedicalDocuments, Inventory*).  
  *Definition of Done (DoD):*  
    - Policies exist and registered; `POLICIES.md` summarizes access matrix.  
  *Ref:*  
    - `/Users/yonassayfu/VSProject/gerayehealthcare/app/Policies`  
    - `/Users/yonassayfu/VSProject/gerayehealthcare/app/Providers/AuthServiceProvider.php`

- [x] **`config/hr.php` usage audit** (prio:P2) (area:model|config) (type:docs)  
  *Task:* Identify exact consumers and standardize access via `config()`.  
  *Why:* Prevents silent regressions when keys change; improves consistency.  
  *Definition of Done (DoD):*  
    - Doc of consumers; remove dead keys; add usage notes.  
  *Ref:* `/Users/yonassayfu/VSProject/gerayehealthcare/config/hr.php`

---

## Commit & Push
1. `git checkout -b fix/phase-2-models-policies`  
2. Commit: `feat(authz): complete morphMap and policy coverage; add POLICIES.md`  
3. `git push origin fix/phase-2-models-policies`

**Reminder:** In PR description, include a “Role → Action → Policy method” table for quick human review.
