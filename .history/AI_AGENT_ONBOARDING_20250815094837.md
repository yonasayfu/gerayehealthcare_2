# AI Agent Onboarding Guide

This document helps any AI agent or developer quickly understand the project’s structure, architecture, current status, and how to proceed safely and efficiently.

Last updated: 2025-08-13


## 1) Project Summary
- Backend: Laravel (PHP)
- Frontend: Vue 3 (TypeScript) with Inertia.js
- Build: Vite
- DB: PostgreSQL
- Tests: Pest
- Goal: Clean, maintainable web app with a future-ready mobile API


## 2) How To Run Locally
- npm run dev — Vite dev server
- npm run build — Production assets

- composer test or php artisan test — Run tests

Environment files: `.env`, `.env.example`


## 3) Key Directories
- app/Http/Controllers/Admin/ — Module controllers (thin, extend BaseController)
- app/Http/Controllers/Base/BaseController.php — Thin controller base
- app/Services/ — Business logic per module (extends BaseService)
- app/Services/BaseService.php — Common CRUD/search/pagination
- app/Services/Validation/Rules/ — Centralized DRY validation rules (40+ classes)
- app/DTOs/ — DTOs used by controllers/services
- app/Http/Traits/ExportableTrait.php — Central CSV/PDF/print logic
- app/Http/Config/ExportConfig.php — Per-model export/print configuration
- resources/views/pdf-layout.blade.php — Current universal PDF view
- routes/web.php — Routes
- database/migrations, database/seeders — Schema and seeds
- resources/js/Pages/Admin/ — Vue pages per module


## 4) Architectural Patterns
- Controllers are thin and extend `BaseController`.
  - Inject service and rules class, optionally a DTO class.
  - Delegate validation to a rules class and operations to a service.
- Services extend `BaseService` for CRUD, pagination, and search hook via `applySearch()`.
- Validation rules are centralized under `app/Services/Validation/Rules/` with static `store()`/`update()` methods.
- DTOs can be plugged into BaseController to shape payloads to services.
- Exports/Prints use `ExportableTrait` with config-driven behavior.


## 5) Export/Print System (Important)
- Centralized in `app/Http/Traits/ExportableTrait.php`.
- Recognized config keys per model in `ExportConfig`:
  - csv: headers, fields, filename_prefix
  - pdf: view, document_title, filename_prefix, orientation
  - current_page: view, columns, with_relations?, include_index?, filename_prefix, orientation
  - all_records: same keys as current_page
  - single_record: view, columns, with_relations?, filename_prefix
- Views: currently `pdf-layout.blade.php` is used universally. If you switch to universal templates, update `view` in configs accordingly.

Known inconsistency to fix:
- Some configs (e.g., Event) still use legacy keys `print_current` / `print_all`. Must be renamed to `current_page` / `all_records` for trait compatibility.


## 6) Validation Layer (Current vs Docs)
- Current code: Uses centralized Rules classes and `$request->validate()` inside `BaseController`.
- Docs mention: `BaseResourceRequest` + unified resource FormRequests. These are not implemented yet.
- Decision needed:
  - Align code to docs by introducing FormRequests; or
  - Update docs to reflect current pattern and keep Rules classes only.


## 7) Current Status Snapshot
- Clean Architecture: Mostly respected. Thin controllers, services, centralized exports and validation rules.
- Export templates: Using `pdf-layout.blade.php`. Docs mention universal-report/single-record templates; these are not present.
- DTOs: Used via BaseController; reflection-based mapping may be brittle.
- Search: Implemented via `BaseService::applySearch()`; ensure each service overrides if search is expected.

Refer to these root docs for deeper context:
- README.md — Overview and commands
- PROJECT_ROADMAP.md — Vision and module inventory
- ISSUE_TRACKER.md — Detailed per-module tasks
- CONTRIBUTING.md — Workflow, code style, module scaffolding templates


## 8) High-Priority Fixes (Start Here)
1) Export config standardization
   - In `app/Http/Config/ExportConfig.php`, convert legacy keys to:
     - `print_current` → `current_page`
     - `print_all` → `all_records`
   - Standardize on `filename_prefix` over `filename`.
   - Ensure each section provides the keys expected by `ExportableTrait`.

2) Validation layer alignment
   - Option A: Implement `BaseResourceRequest` + 1–2 pilot resource FormRequests. Update controllers to type-hint FormRequests.
   - Option B: Keep current pattern and update docs accordingly.

3) PDF view/template alignment
   - Either keep `pdf-layout.blade.php` as the universal template and update docs, or add the universal templates and point configs to them.

4) Logging hygiene
   - Remove or env-guard verbose `Log::info()` calls in `BaseController@show` and `edit`.


## 9) “Context Pack” To Share With Any Agent
Always provide these files when engaging an external AI or dev:
- PROJECT_ROADMAP.md
- DATABASE_SCHEMA.md
- ISSUE_TRACKER.md (filtered to the target issue)
- Only directly related code files: controller, service, model, Vue pages, routes, and relevant config (e.g., `ExportConfig.php`)
- This AI_AGENT_ONBOARDING.md (this file)

Issue brief template (copy/paste):
- Title: <clear, single-scope change>
- Module: <Patients | Staff | Inventory | ...>
- Current behavior:
- Expected behavior:
- Files provided:
- Routes/UI entry points:
- Constraints:
- Done criteria:


## 10) Adding a New Module (Checklist)
Follow CONTRIBUTING.md “Clean Architecture Module Implementation Guide”. Summary:
- php artisan make:model [ModuleName] -mfsc
- Create service: `app/Services/[ModuleName]/[ModuleName]Service.php`
- Validation rules: `app/Services/Validation/Rules/[ModuleName]Rules.php`
- Vue pages: `resources/js/Pages/Admin/[ModuleName]s/`
- Add resource route in `routes/web.php`
- Optional: Add export config in `ExportConfig`
- Optional: Add DTOs in `app/DTOs/`


## 11) Guardrails / Architecture Rules
- Controllers do not access Eloquent directly beyond route-model binding; use services.
- Services do not depend on HTTP or Inertia.
- Validation is centralized via Rules (or FormRequests if implemented).
- Export/print only via `ExportableTrait` and `ExportConfig`.
- Keep pagination default at 5 unless required otherwise.


## 12) Quick Triage Flow For New Issues
1) Read ISSUE_TRACKER.md for the module.
2) Open controller/service/rules for that module.
3) If export/print issue: check `ExportConfig` keys and `pdf-layout.blade.php`.
4) If validation issue: check corresponding Rules class.
5) If index/search issue: check service’s `applySearch()` and `getAll()` usage.
6) Confirm routes and Vue pages align with controller prop names.


## 13) Known Decisions Pending
- Choose validation approach (FormRequests vs current Rules-only) and update docs/code accordingly.
- Normalize export config keys across all models.
- Decide on final universal PDF template(s) and reference them consistently.


## 14) Contacts / Ownership
- App owner: Geraye Healthcare Platform
- Primary maintainer: You (repo owner). AI agents should follow this guide and the workflow in `CONTRIBUTING.md`.


## 15) Done Definition For Fixes
- Code compiles and tests pass
- UI screens for the module load without console errors
- Exports/prints preview correctly with consistent styling
- Validation errors are user-friendly
- Docs updated (ISSUE_TRACKER.md and this file if architecture changed)
