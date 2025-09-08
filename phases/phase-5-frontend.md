# Phase 5 — Frontend (Vue 3 + Inertia)

> Architecture alignment: UI consumes Resource-based APIs and Inertia props; centralized print/export; avoid ad-hoc axios where server-rendered patterns exist.

Unify UI/UX, centralize print/export, fix search+pagination, and complete module UIs.

---

## Global & Layouts
- [x] **AppLayout inventory alert code** (prio:P2)  
  *Ref:* `/Users/yonassayfu/VSProject/gerayehealthcare/resources/js/layouts/AppLayout.vue`

- [x] **Auth layout (Liquid Glass)** (prio:P2)  
  *Task:* Combine `AuthCardLayout` and `AuthSplitLayout`.  
  *Ref:* `/Users/yonassayfu/VSProject/gerayehealthcare/resources/js/layouts/AuthLayout.vue`  
  `/Users/yonassayfu/VSProject/gerayehealthcare/resources/js/layouts/auth/*`

- [ ] **GlobalSearch + pagination state** (prio:P1)  
  *Task:* Persist query to URL; server returns filtered links.  
  *Ref:* `/Users/yonassayfu/VSProject/gerayehealthcare/resources/js/components/GlobalSearch.vue`

- [ ] **ChatModal UI polish** (prio:P2)  
  *Task:* Search field, message body spacing, actions menu behavior.  
  *Ref:* `/Users/yonassayfu/VSProject/gerayehealthcare/resources/js/components/ChatModal.vue`

## CSS & Printing
- [ ] **Clean `app.css`** (prio:P2)  
  *Ref:* `/Users/yonassayfu/VSProject/gerayehealthcare/resources/css/app.css`

- [x] **Central print stylesheet** (prio:P1)  
  *Task:* Use single `print.css`; remove per-module `printCurrent.vue` / `printAll.vue`.  
  *Ref:*  
    - `/Users/yonassayfu/VSProject/gerayehealthcare/resources/css/print.css`  
    - `/Users/yonassayfu/VSProject/gerayehealthcare/resources/js/components/print/*`

- [x] **`responsive-fixes.css` purpose** (prio:P3)  
  *Ref:* `/Users/yonassayfu/VSProject/gerayehealthcare/resources/css/responsive-fixes.css`

## Public & Welcome
- [ ] **Welcome.vue** (prio:P3)  
  *Task:* Align to product template or guard route.  
  *Ref:* `/Users/yonassayfu/VSProject/gerayehealthcare/resources/js/pages/Welcome.vue`

- [ ] **Generic Dashboard.vue relevance** (prio:P3)  
  *Ref:* `/Users/yonassayfu/VSProject/gerayehealthcare/resources/js/pages/Dashboard.vue`

## Staff Area
- [ ] **MyVisits** (geo + API + roles) (prio:P1)  
  *Ref:* `/Users/yonassayfu/VSProject/gerayehealthcare/resources/js/pages/Staff/MyVisits/Index.vue`  
  `/Users/yonassayfu/VSProject/gerayehealthcare/resources/js/pages/Staff/MyVisits/*`

- [ ] **MyLeaveRequests** (prio:P1)  
  *Ref:* `/Users/yonassayfu/VSProject/gerayehealthcare/resources/js/pages/Staff/MyLeaveRequests/*`

- [ ] **MyEarnings** (prio:P1)  
  *Ref:* `/Users/yonassayfu/VSProject/gerayehealthcare/resources/js/pages/Staff/MyEarnings/*`

- [ ] **MyAvailability** + admin clash prevention (prio:P1)  
  *Ref:* `/Users/yonassayfu/VSProject/gerayehealthcare/resources/js/pages/Staff/MyAvailability/*`

## Auth & Settings
- [ ] **Auth pages** + local mail test (prio:P1)  
  *Ref:* `/Users/yonassayfu/VSProject/gerayehealthcare/resources/js/pages/auth/*`

- [ ] **Settings/Password.vue** (prio:P1)  
  *Ref:* `/Users/yonassayfu/VSProject/gerayehealthcare/resources/js/pages/settings/Password.vue`

## Module-Specific (Admin*)
- [ ] **VisitServices/Index.vue** (docs & location cols; geo parity) (prio:P1)  
  *Ref:* `/Users/yonassayfu/VSProject/gerayehealthcare/resources/js/pages/Admin/VisitServices/Index.vue`

- [ ] **Users module** (link to Staff; auth alignment) (prio:P1)  
  *Ref:* `/Users/yonassayfu/VSProject/gerayehealthcare/resources/js/pages/Admin/Users/*`

- [ ] **TaskDelegations** (notif persistence, self-assign/transfer, audit) (prio:P1)  
  *Ref:* `/Users/yonassayfu/VSProject/gerayehealthcare/resources/js/pages/Admin/TaskDelegations/Index.vue`

- [ ] **Suppliers cleanup + forms** (prio:P2)  
  *Ref:* `/Users/yonassayfu/VSProject/gerayehealthcare/resources/js/pages/Admin/Suppliers/*`

- [ ] **StaffPayouts** (admin edit/revert; staff history/requests) (prio:P1)  
  *Ref:* `/Users/yonassayfu/VSProject/gerayehealthcare/resources/js/pages/Admin/StaffPayouts/Index.vue`

- [ ] **StaffAvailabilities** clash logic (prio:P1)  
  *Ref:* `/Users/yonassayfu/VSProject/gerayehealthcare/resources/js/pages/Admin/StaffAvailabilities/*`

- [ ] **SharedInvoices CRUD + UI** (prio:P1)  
  *Ref:* `/Users/yonassayfu/VSProject/gerayehealthcare/resources/js/pages/Admin/SharedInvoices/*`

- [ ] **Services CRUD + style** (prio:P1)  
  *Ref:* `/Users/yonassayfu/VSProject/gerayehealthcare/resources/js/pages/Admin/Services/Index.vue`

- [ ] **Roles dynamic perms; sync with middleware & sidebar** (prio:P1)  
  *Ref:* `/Users/yonassayfu/VSProject/gerayehealthcare/resources/js/pages/Admin/Roles/Index.vue`

- [ ] **ReferralDocuments & Referrals** (create/edit; links to invoices/notifications; exports) (prio:P1)  
  *Ref:*  
    - `/Users/yonassayfu/VSProject/gerayehealthcare/resources/js/pages/Admin/ReferralDocuments/*`  
    - `/Users/yonassayfu/VSProject/gerayehealthcare/resources/js/pages/Admin/Referrals/*`

- [ ] **Prescriptions** (doctor/patient access; shareable; mobile) (prio:P1)  
  *Ref:* `/Users/yonassayfu/VSProject/gerayehealthcare/resources/js/pages/Admin/Prescriptions/*`

- [ ] **Patients** (privacy; prune files) (prio:P1)  
  *Ref:* `/Users/yonassayfu/VSProject/gerayehealthcare/resources/js/pages/Admin/Patients/*`

- [ ] **Partners** (Account Manager field; CSV; central print) (prio:P2)  
  *Ref:* `/Users/yonassayfu/VSProject/gerayehealthcare/resources/js/pages/Admin/Partners/Index.vue`

- [ ] **PartnerEngagements** (merge/justify; reminders) (prio:P3)  
  *Ref:* `/Users/yonassayfu/VSProject/gerayehealthcare/resources/js/pages/Admin/PartnerEngagements/*`

- [ ] **PartnerCommissions** (dropdown data; CRUD) (prio:P2)  
  *Ref:* `/Users/yonassayfu/VSProject/gerayehealthcare/resources/js/pages/Admin/PartnerCommissions/*`

- [ ] **Marketing suite** (Tasks/Leads/Campaigns/Budgets/Analytics/LandingPages) (prio:P1)  
  *Fix:* standardize delete modal; remove `alert()`; fix print/export; remove footer timestamp; analytics live from DB; budget deltas shown.  
  *Ref:*  
    - `/Users/yonassayfu/VSProject/gerayehealthcare/resources/js/pages/Admin/MarketingTasks/*`  
    - `/Users/yonassayfu/VSProject/gerayehealthcare/resources/js/pages/Admin/MarketingLeads/*`  
    - `/Users/yonassayfu/VSProject/gerayehealthcare/resources/js/pages/Admin/MarketingCampaigns/*`  
    - `/Users/yonassayfu/VSProject/gerayehealthcare/resources/js/pages/Admin/MarketingBudgets/*`  
    - `/Users/yonassayfu/VSProject/gerayehealthcare/resources/js/pages/Admin/MarketingAnalytics/*`  
    - `/Users/yonassayfu/VSProject/gerayehealthcare/resources/js/pages/Admin/LandingPages/*`

- [ ] **LeaveRequests (Admin)** (allow own request via staff side; manage approvals) (prio:P1)  
  *Ref:* `/Users/yonassayfu/VSProject/gerayehealthcare/resources/js/pages/Admin/LeaveRequests/*`

- [ ] **Invoices** (linkages; UI; pagination indicator; Incoming.vue relationships) (prio:P1)  
  *Ref:* `/Users/yonassayfu/VSProject/gerayehealthcare/resources/js/pages/Admin/Invoices/*`

- [ ] **Inventory** (create transaction class, fix edit type/id; unify print; alerts undefined id; items print UI) (prio:P1)  
  *Ref:*  
    - `/Users/yonassayfu/VSProject/gerayehealthcare/resources/js/pages/Admin/InventoryTransactions/*`  
    - `/Users/yonassayfu/VSProject/gerayehealthcare/resources/js/pages/Admin/InventoryItems/*`  
    - `/Users/yonassayfu/VSProject/gerayehealthcare/resources/js/pages/Admin/InventoryAlerts/*`

- [ ] **Events** (CRUD participants/broadcasts/recommendations; guest recommend API; pagination; central print) (prio:P1)  
  *Ref:*  
    - `/Users/yonassayfu/VSProject/gerayehealthcare/resources/js/pages/Admin/Events/*`  
    - `/Users/yonassayfu/VSProject/gerayehealthcare/resources/js/pages/Admin/EventParticipants/*`  
    - `/Users/yonassayfu/VSProject/gerayehealthcare/resources/js/pages/Admin/EventBroadcasts/*`  
    - `/Users/yonassayfu/VSProject/gerayehealthcare/resources/js/pages/Admin/EventRecommendations/*`  
    - `/Users/yonassayfu/VSProject/gerayehealthcare/resources/js/pages/Admin/EventStaffAssignments/*`

- [ ] **CaregiverAssignments** clash rules (prio:P1)  
  *Ref:* `/Users/yonassayfu/VSProject/gerayehealthcare/resources/js/pages/Admin/CaregiverAssignments/*`

- [ ] **Admin Dashboard** (live charts; remove static stubs; prefer Inertia over ad-hoc axios) (prio:P1)  
  *Ref:* `/Users/yonassayfu/VSProject/gerayehealthcare/resources/js/pages/Admin/Dashboard/Index.vue`

## Search & Pagination
- [ ] **Fix pagination state with filters** (prio:P1)  
  *Task:* Keep query params in route, server returns filtered `links`.  
  *Ref:* All list views affected.

---

### Clean Architecture Alignment
- Keep page components dumb; move domain logic to services; use composables for UI state.
- Use typed TS interfaces for props/responses; centralize pagination/search patterns.

### OriginalIssue Traceability
- CSS and print stylesheet: items 3, 5, 7, 83, 84
- Localizing assets in app.blade.php: item 17
- TS types consolidation: item 19
- Welcome/Dashboard views: items 20, 21
- Search + pagination bug: item 32 (and many list pages)
- ChatModal polish: item 81
- GlobalSearch partial coverage: item 80
- Numerous module UIs: items 33, 35–37, 39–47, 49–61, 62–71, 73–76, 79, 82, 85–89, 91–100, 102–106, 114–121, 124–148, 150

## Commit & Push
1. `git checkout -b fix/phase-5-frontend`  
2. Commit: `feat(ui): unify layouts, central print/export, fix search+pagination, complete admin modules`  
3. `git push origin fix/phase-5-frontend`

**Reminder:** Attach before/after screenshots for at least 5 modules and a GIF for search+pagination behavior.
