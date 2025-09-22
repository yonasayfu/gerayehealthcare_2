# ROUTING.md

This document defines route grouping, naming, and middleware conventions for Geraye Healthcare. It is the single source of truth for web and API route structure so the frontend and mobile clients remain stable.

## Naming conventions

- Admin (web UI)
  - Route names: `admin.*`
  - URL prefix: `/dashboard` (historical UX choice)
  - Example: `admin.patients.index` -> `/dashboard/patients`

- Staff (web UI)
  - Route names: `staff.*`
  - URL prefix: `/dashboard`
  - Example: `staff.my-availability.index` -> `/dashboard/my-availability`

- Accountant/Finance
  - Route names: `reconciliation.*`
  - URL prefix: `/reconciliation`

- Reports (web UI)
  - Route names: `reports.*`
  - URL prefix: `/dashboard/reports`

- API V1 (mobile/clients)
  - Route names are rarely used on the client; the structure is `api/v1/...`
  - Use Resource responses and consistent pagination envelopes where applicable.

## Grouping and structure (web)

- All authenticated UI routes live under `Route::middleware(['auth','verified'])`.
- Admin UI is grouped under `/dashboard` with `->name('admin.')`.
- Staff UI is grouped under `/dashboard` with `->name('staff.')`.
- Domain subgroups use prefixes and names, e.g., `prefix('reports')->name('reports.')`.

Domain ordering inside admin group (recommended):
- Dashboard KPIs (overview-data, series, reports/*)
- Patients (export/print routes BEFORE resource)
- Staff (export/print BEFORE resource)
- Inventory (export/print BEFORE resource)
- Invoices/Shared Invoices
- Events / Recommendations / Participants / Staff Assignments / Broadcasts
- Visit Services / Staff Availability / Leave Requests / Task Delegations
- Medical & Referral Documents / Prescriptions
- Roles & Users
- Insurance domain (policies, claims, calendars)

Export/Print placement:
- Always place specific routes (export, print-all, print-current, print-single) BEFORE the corresponding `Route::resource(...)` to avoid the `show` route capturing them.

Example (Admin):

```php
Route::middleware(['auth','verified'])->prefix('dashboard')->name('admin.')->group(function () {
    Route::resource('patients', PatientController::class);
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('service-volume', [ServiceVolumeController::class, 'index'])->name('service-volume');
    });
});
```

Example with export/print ordering:

```php
Route::middleware(['auth','verified'])->prefix('dashboard')->name('admin.')->group(function () {
    Route::middleware('can:view patients')->group(function () {
        Route::get('patients/export', [PatientController::class, 'export'])->name('patients.export');
        Route::get('patients/print-all', [PatientController::class, 'printAll'])->name('patients.printAll');
        Route::get('patients/print-current', [PatientController::class, 'printCurrent'])->name('patients.printCurrent');
        Route::get('patients/{patient}/print', [PatientController::class, 'printSingle'])->name('patients.printSingle');
    });
    Route::resource('patients', PatientController::class);
});
```

## Marketing routes

- Currently defined in `routes/marketing.php` and required from `routes/web.php`.
- All routes are already grouped under Admin (`/admin` in that file) and Staff prefixes with the proper names (`admin.*`, `staff.*`).
- P2 task: merge into `routes/web.php` under the existing Admin `/dashboard` group to reduce file count, keeping route names the same to avoid breaking the frontend.

## Middleware patterns

- Authorization: use policies via `can:` middleware (e.g., `->middleware('can:view patients')`).
- Throttling: apply per-route throttling for heavy actions (e.g., messaging send/delete/download).
- Signed routes: use `middleware('signed')` for public PDF links.

## API (V1) rules

- Prefix: `/api/v1`
- Auth: Sanctum
- Responses: use Resources and return pagination metadata with `data` and `pagination` keys.
- Rate limiting: `Limit::perMinute(60)` default; adjust at the route level for heavy endpoints.
- Group messaging (API): expose create/list/index/store/update/destroy/react/download consistently under `/api/v1/groups/...`; keep DMs under `/api/v1/messages/...`.
- Visit Services (mobile): include `check-in` and `check-out` endpoints, throttle write actions, and validate geo if provided.

## Stability rules

- Do not rename routes used by the frontend (Ziggy) without mapping aliases.
- Merges of separate route files must keep route names intact.
- Avoid route-level heavy closures; delegate to controllers.
 - Keep dev-only diagnostics under `if (app()->environment('local'))` so production has zero exposure and overhead.

## Verification

- List all routes: `php artisan route:list`
- Filter critical groups: `php artisan route:list | rg -n 'patients|events|messages|api-docs|optimized'`
- Confirm dev-only hidden in prod: `APP_ENV=production php artisan route:list | rg performance`

