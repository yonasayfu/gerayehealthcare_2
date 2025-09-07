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
