# Phase 1 — Migrations & Database Relationships (Top Priority)

> Architecture alignment: migrate schema first, then align DTOs and models per Clean Architecture guide (Phase 3 doc).

Fix the database foundation: relationships, constraints, factories/seeders realism, DTO parity.

---

- [x] **Audit all migration classes & foreign keys** (prio:P1) (area:db) (type:refactor)  
  *Task:* Verify FK directions, cascade/restrict, indexes, pivot naming, soft deletes.  
  *Why:* Prevents orphan data, integrity errors, and inconsistent joins.  
  *Definition of Done (DoD):*  
    - `php artisan migrate:fresh --seed` passes.  
    - ERD generated and reviewed.  
    - No FK or index warnings.  
  *Ref:* `/Users/yonassayfu/VSProject/gerayehealthcare/database/migrations`

- [x] **Factories & Seeders aligned with relationships** (prio:P1) (area:db) (type:refactor)  
  *Task:* Seed realistic relational data (no arbitrary 7/10 items).  
  *Why:* Enables reliable E2E testing and demo data.  
  *Definition of Done (DoD):*  
    - Factories respect FKs and realistic hierarchies.  
    - `php artisan db:seed` produces coherent demo.  
  *Ref:*  
    - `/Users/yonassayfu/VSProject/gerayehealthcare/database/factories`  
    - `/Users/yonassayfu/VSProject/gerayehealthcare/database/seeders`

- [ ] **DTO alignment check** (prio:P1) (area:db|model) (type:consistency)  
  *Task:* Ensure DTO fields mirror migration columns and model casts.  
  *Why:* Mismatches (e.g., missing `status` in `CreateInventoryRequestDTO`) break validation/serialization.  
  *Definition of Done (DoD):*  
    - DTOs updated; request validation passes; feature tests added.  
  *Ref:* `/Users/yonassayfu/VSProject/gerayehealthcare/app/DTOs`

---

### Clean Architecture Alignment
- Keep migrations authoritative; then align Model casts and DTO fields to avoid drift.
- Prefer FormRequests to validate DTO shapes after migrations are stable.

### OriginalIssue Traceability
- Comprehensive relationship audit and seed realism: item 4
- Employee Insurance Records relationships: item 31
- DTO mismatches (e.g., missing status in CreateInventoryRequestDTO): item 65

## Commit & Push
1. `git checkout -b fix/phase-1-migrations`  
2. Commit: `refactor(db): fix FKs, align factories/seeders, sync DTOs`  
3. `git push origin fix/phase-1-migrations`
**NOTE** FOR this task let take phase-0 repo as a main and after you push each phase compilation let merge with phase-0, and don't delete each created branch.
**Reminder:** Attach ERD (image or md) to the PR; paste seeding sample outputs for quick review.
