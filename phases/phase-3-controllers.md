# Phase 3 — Controllers, Services & API

Make controllers thin, move logic to services, fix requests, and normalize API responses.


**NOTE**
THIS PAth is the orginal issue before i enance with the Ai agent so it helps you clarify my aim/goal, it is unorder but each issue mentioned in phasee-0 up to phase 10 are mentioned here /Users/yonassayfu/VSProject/gerayehealthcare/phases/OrginalIssue.md

**NOTE** END
---

- [x] **Thin controllers** (prio:P1) (area:controller) (type:refactor)
  *Task:* Keep only `__construct`, `index`, `store`, `show`, `update`, `destroy`; shift logic to services.  
  *Why:* Improves testability and maintainability.  
  *Definition of Done (DoD):*  
    - Controllers <200 lines; services cover business logic.  
  *Ref:* `/Users/yonassayfu/VSProject/gerayehealthcare/app/Http/Controllers/*`

- [x] **Service layer consistency** (prio:P1) (area:service) (type:refactor)
  *Task:* Normalize `app/Services` layout; keep `App\Rules` separate but used by services; document high-logic flows (VisitService, Inventory, Invoices).  
  *Why:* Reduces duplication; promotes reuse.  
  *Definition of Done (DoD):*  
    - Services unit-tested; DI-enabled.  
  *Ref:*  
    - `/Users/yonassayfu/VSProject/gerayehealthcare/app/Services`  
    - `/Users/yonassayfu/VSProject/gerayehealthcare/app/Rules`

- [x] **Message & Group messaging fixes** (prio:P1) (area:controller|feature)
  *Task:* Allow users to delete their own messages; persistent groups; open group creation to authorized roles; ensure secure file download for recipients.  
  *Why:* Current behavior limits usability and loses groups on reload.  
  *Definition of Done (DoD):*  
    - RBAC tests; groups persist; attachment downloads scoped to recipients.  
  *Ref:*  
    - `/Users/yonassayfu/VSProject/gerayehealthcare/app/Http/Controllers/MessageController.php`  
    - `/Users/yonassayfu/VSProject/gerayehealthcare/app/Http/Controllers/GroupMessageController.php`  
    - `/Users/yonassayfu/VSProject/gerayehealthcare/app/Http/Controllers/GroupController.php`

- [ ] **Insurance controllers consolidation** (prio:P2) (area:controller) (type:refactor)  (if we can let put inside in admin controller and make sure it not break the over all code and the cascade moduels)
  *Task:* Merge `Controllers/Insurance` into standard module pattern or justify separation.  
  *Why:* Inconsistent architecture makes routing/policies harder.  
  *Definition of Done (DoD):*  
    - Clear module boundary; thin controllers.  
  *Ref:* `/Users/yonassayfu/VSProject/gerayehealthcare/app/Http/Controllers/Insurance/*`

- [ ] **Reports controllers refactor** (prio:P2) (area:controller|export)  
  *Task:* Unify report endpoints; index page lists report types; stream/queue heavy exports.  
  *Why:* Centralizes logic and UX for reporting.  
  *Definition of Done (DoD):*  
    - Single reports hub; queued jobs for large datasets.  
  *Ref:* `/Users/yonassayfu/VSProject/gerayehealthcare/app/Http/Controllers/Reports/*`

- [ ] **BaseController review** (prio:P2) (area:controller) (type:docs)  
  *Task:* Ensure shared concerns (authz, responses) are centralized and reused.  
  *Why:* Avoids duplication and drift.  
  *Ref:* `/Users/yonassayfu/VSProject/gerayehealthcare/app/Http/Controllers/Base/BaseController.php`

- [ ] **Requests layer fixes** (prio:P1) (area:request)  
  *Task:*  
    - `VisitService` request: geo validation for web/mobile.  
    - `Settings` requests: prod-ready email reset.  
    - `Auth\LoginRequest`: support Ethiopian phone formats (`0XXXXXXXXX`, `+251XXXXXXXXX`) and uniqueness; align with registration.  
  *Why:* Validation/security correctness.  
  *DoD:* Request tests added; mobile E2E happy path passes.  
  *Ref:*  
    - `/Users/yonassayfu/VSProject/gerayehealthcare/app/Http/Requests/VisitService`  
    - `/Users/yonassayfu/VSProject/gerayehealthcare/app/Http/Requests/Settings`  
    - `/Users/yonassayfu/VSProject/gerayehealthcare/app/Http/Requests/Auth/LoginRequest.php`  
    - `/Users/yonassayfu/VSProject/gerayehealthcare/app/Http/Controllers/Auth/RegisteredUserController.php`

- [x] **API surface audit (V1)** (prio:P1) (area:api)
  *Task:* Ensure mobile-required modules are exposed; normalize return types to Resources (avoid mixing `response()->json` with `*Resource`).  
  *Why:* Consistency for Flutter/mobile clients.  
  *DoD:* Resource-based responses; basic OpenAPI sketch.  
  *Ref:*  
    - `/Users/yonassayfu/VSProject/gerayehealthcare/app/Http/Controllers/Api/V1/*`  
    - `/Users/yonassayfu/VSProject/gerayehealthcare/app/Http/Requests/Api/*`

- [x] **Middleware review** (prio:P2) (area:middleware)
  *Task:* Scope `QueryCacheMiddleware`, `CacheExpensiveQueries`, `PermissionMiddleware` to heavy routes; add invalidation strategy.  
  *Why:* Performance without stale data.  
  *Ref:* `/Users/yonassayfu/VSProject/gerayehealthcare/app/Http/Middleware/*`

- [ ] **Events/Listeners/Notifications/Mail** (prio:P2) (area:notify)  
  *Task:* Verify `NewCaregiverAssignment`, `TaskDelegationAssigned`, `InsuranceClaimEmail` + in-app bell parity; queue mail.  
  *Why:* Reliable comms; dev fake mail + prod-ready.  
  *Ref:*  
    - `/Users/yonassayfu/VSProject/gerayehealthcare/app/Notifications/*`  
    - `/Users/yonassayfu/VSProject/gerayehealthcare/app/Mail/InsuranceClaimEmail.php`  
    - `/Users/yonassayfu/VSProject/gerayehealthcare/app/Events/*`  
    - `/Users/yonassayfu/VSProject/gerayehealthcare/app/Listeners/*`  
    - `/Users/yonassayfu/VSProject/gerayehealthcare/app/Providers/EventServiceProvider.php`

- [ ] **Rate limiter sanity** (prio:P3) (area:provider)  
  *Task:* Review `60/min` defaults vs auth/data routes.  
  *Ref:* `/Users/yonassayfu/VSProject/gerayehealthcare/app/Providers/RouteServiceProvider.php`

---

## Commit & Push
1. `git checkout -b fix/phase-3-controllers-services-api`  
2. Commit: `refactor(http): thin controllers, service layer, requests & API V1 normalization`  
3. `git push origin fix/phase-3-controllers-services-api`

**Reminder:** Include a short OpenAPI snippet or resource examples in the PR for mobile devs.

---

## Clean Architecture (Onion) Guide — Controllers, Services, DTOs, Rules

This is the canonical source for controller/service/DTO conventions to be applied across all phases.

This section documents the conventions we follow so any developer or AI agent can quickly understand and continue the work without breaking the frontend.

### Layer responsibilities

- Controller (web/UI or API)
  - Accepts a typed Request (prefer FormRequest).
  - Authorizes the action (policy/gate) when needed.
  - Translates Request -> DTO/array and calls exactly one Service method.
  - Returns a response: Inertia view (web) or Resource/JSON (API).
  - May do small UI-only preloads (e.g., select options) in create/edit.
  - Must not handle business logic, heavy data shaping, or direct DB writes.

- Service (application layer)
  - Transaction boundaries and orchestration.
  - Business rules, data shaping, and calls to Models/Repositories.
  - Emits domain events, dispatches notifications, queues jobs if needed.
  - Returns domain models/DTOs ready for presentation.

- DTOs (input/output contracts)
  - Define explicit shapes for create/update/domain actions.
  - Keep constructors/immutability simple; optional static fromRequest(Request) helpers.

- Validation (Rules/FormRequests)
  - Prefer FormRequest per action: StoreXxxRequest, UpdateXxxRequest, DomainActionXxxRequest.
  - If we use Rules classes (e.g., StaffRules), keep only rule arrays here and call them from FormRequests or BaseController.

- Resources (API only)
  - Normalize API response shapes; avoid mixing raw response()->json and Resource responses within the same module.

- Policies
  - Express authorization rules; Controllers call authorize() or use policy middleware.

- Events/Notifications/Mail
  - Service emits events and/or sends notifications; listeners handle side-effects.

- Export/Print (web)
  - Prefer ExportableTrait + BaseController handlers (handleExport, handlePrintAll, handlePrintCurrent, handlePrintSingle) with a per-module config.

### Staff module — reference workflow

Use this as the canonical example of a thin controller + service-driven flow.

- Create staff (web)
  1) Route -> POST admin/staff
  2) Controller: StaffController@store (FormRequest: StoreStaffRequest)
  3) Controller calls StaffService->create(CreateStaffDTO)
  4) StaffService
     - Begins transaction
     - Creates User + Staff, assigns role(s)
     - Emits StaffCreated event, sends notifications if needed
     - Commits transaction
  5) Controller redirects to admin.staff.index with banner

- Update staff (web)
  1) Route -> PUT/PATCH admin/staff/{id}
  2) Controller: StaffController@update (FormRequest: UpdateStaffRequest)
  3) Controller calls StaffService->update($id, UpdateStaffDTO)
  4) Service applies rules, persists changes, may emit events
  5) Controller redirects with success banner

- List staff (web)
  1) Route -> GET admin/staff
  2) Controller: StaffController@index
  3) StaffService->getAll($filters) (sorting, pagination, eager loads)
  4) Controller returns Inertia view with data + filters

- Export/Print (web)
  - Controller delegates to BaseController trait handlers with a module config.
  - Service may provide export data if complex (large reports may queue jobs).

- Show/Edit/Create (web)
  - Controller keeps UI preloads minimal (e.g., config('hr.departments')), heavy lookups via service as needed.

- API (if/when needed)
  - Api\V1\StaffController returns Resources; uses BaseApiController helpers.

### What methods each layer should have

- Controller (web, Admin/*)
  - __construct, index, show, create, edit, store, update, destroy
  - Optional domain actions: approve, generate, shareLink, etc. — but they must delegate to service
  - Optional: export, printAll, printCurrent, printSingle (prefer trait handlers)

- Service (StaffService)
  - getAll(Request|array filters), getById($id, $with = [])
  - create(CreateStaffDTO|array $payload)
  - update(int $id, UpdateStaffDTO|array $payload)
  - delete(int $id)
  - export/print helpers if not using the trait pattern
  - Domain actions: assignRole, revokeRole, adjustAvailability, etc.

- DTOs (App\DTOs)
  - CreateStaffDTO: user_id?, department, position, phone, etc.
  - UpdateStaffDTO: same fields but optional/nullable

- Validation
  - StoreStaffRequest (rules for create)
  - UpdateStaffRequest (rules for update)
  - If a Rules class exists (StaffRules), the FormRequests can source rules from it.

- Policies
  - StaffPolicy: viewAny, view, create, update, delete, export

- Resources (API)
  - StaffResource, StaffCollection

### Definition of a thin controller (DoD)

- No business logic (all in Service).
- No inline Validator usage; use FormRequest (or BaseController validation harness calling Rules).
- At most one service call per action.
- Only small, UI-related select lists in create/edit.
- Consistent response types (Inertia for web, Resource for API).

### Anti-patterns to avoid

- Data shaping in controllers (groupBy/map/joins). Move to Service.
- Multiple services from a single controller action without orchestration in a single service.
- Persisting or deleting models directly in controllers.
- Mixed response styles for API (Resource + manual JSON).

### Refactor recipe (apply to each module)

1) Replace inline Validator with FormRequests (StoreXxxRequest, UpdateXxxRequest).
2) Move all business logic from controllers to services; one service method per action.
3) Keep create/edit preloads minimal in controllers; load heavy data via service.
4) Standardize export/print via ExportableTrait + BaseController handlers (unless API-only).
5) Normalize API to Resource responses.
6) Add or update policies and register them in AuthServiceProvider.
7) Add tests or pending notes for service behaviors (to be covered in the testing phase).

### Mapping staff module in this codebase today

- Controller: app/Http/Controllers/Admin/StaffController.php (already extends BaseController; mostly thin)
- Service: app/Services/StaffService.php (owns business rules)
- Rules: app/Services/Validation/Rules/StaffRules.php (can be wrapped by FormRequests)
- DTOs: app/DTOs/CreateStaffDTO.php, UpdateStaffDTO.php
- Policy: app/Policies/StaffPolicy.php (ensure registered)
- API (if needed): app/Http/Controllers/Api/V1/StaffController.php (should return Resources)

Once we apply this convention uniformly (Invoices, Referrals, Inventory, etc.), the codebase will have consistent thin controllers, predictable services, and easy-to-maintain tests.
