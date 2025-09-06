# GerayeHealthcare — Phased Backlog

> March in order, not in haste. We build the foundation first, then the walls, then the light.  
> **Flow:** Migrations → Models → Controllers/Services → Routes → Frontend → Exports → Notifications/Logs → Storage/Assets → Cleanup → Knowledge Base.

This folder contains one Markdown file per phase. Each file is a checklist with **Task / Why / DoD / Ref** sections and **exact paths** so both humans and AI agents can navigate without guesswork.

---

## 📚 Index

- **Phase 0 — Baseline & Housekeeping** → [`phase-0-baseline.md`](./phase-0-baseline.md)
- **Phase 1 — Migrations & Database Relationships** → [`phase-1-migrations.md`](./phase-1-migrations.md)
- **Phase 2 — Models, Policies & Morphs** → [`phase-2-models.md`](./phase-2-models.md)
- **Phase 3 — Controllers, Services & API** → [`phase-3-controllers.md`](./phase-3-controllers.md)
- **Phase 4 — Routes & Structure** → [`phase-4-routes.md`](./phase-4-routes.md)
- **Phase 5 — Frontend (Vue 3 + Inertia)** → [`phase-5-frontend.md`](./phase-5-frontend.md)
- **Phase 6 — Exports, Print, Reports** → [`phase-6-exports.md`](./phase-6-exports.md)
- **Phase 7 — Notifications, Email, Logs & Audit** → [`phase-7-notifications.md`](./phase-7-notifications.md)
- **Phase 8 — Storage & Assets** → [`phase-8-storage.md`](./phase-8-storage.md)
- **Phase 9 — Cleanup & Deletions** → [`phase-9-cleanup.md`](./phase-9-cleanup.md)
- **Phase 10 — Knowledge Base & Documentation** → [`phase-10-docs.md`](./phase-10-docs.md)

> Tip: Keep these files in `phases/` at the project root. Each file ends with a **Commit & Push** block plus a **Reminder** for PR hygiene.

---

## 🧭 Working Agreement (for AI Agents & Humans)

1. **Work one phase at a time.** Do **not** jump phases.  
2. **Follow the file for the current phase** (e.g., `phases/phase-3-controllers.md`) **top-to-bottom**.  
3. Prefer finishing the **entire phase in one go**. If not feasible, propose a **short sub-roadmap** and proceed stepwise.
4. Each task includes **Ref** (full paths). Touch only the referenced areas unless the task explicitly says otherwise.
5. When a phase is complete:
   - Update checkboxes to `- [x]`.
   - Produce a **summary of changes** + **testing steps**.
   - **Commit & push** using the branch name in the phase file.
   - Open a PR titled `Phase X — <name>`.

---

## ✅ Definition of Done (global)

A phase is “done” when:
- All checkboxes are ticked in that phase’s file.
- CI is green (linters/tests/build).
- **DoD** bullets inside each task are satisfied.
- A short **test guide** is included in the PR body (how to verify).
- Sensitive changes (auth, payments, patients) have policies and tests.

---

## 🔧 Local Commands (reference)

- Install & build:  
  ```bash
  composer install
  npm install
  npm run build
  php artisan migrate:fresh --seed
  php artisan test
