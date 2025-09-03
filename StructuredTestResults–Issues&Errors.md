
# 📋 Structured Test Results – Issues & Errors

---

## 1. **Message Module**

* **File Attachments**

  * Cannot attach files.
* **Sending Messages**

  * As user (e.g., Yonas) cannot send to legacy super admin/admin.
* **Message Fetch**

  * Stored messages not loading or not fetched.
* **Message Actions**

  * Edit/Delete/3-dot options don’t trigger properly.
  * Only works when console window is open; dialog closes unexpectedly.
* **Search UI**

  * Placeholder misaligned outside search box.
* **User List**

  * Full name shown but initial not styled (missing circle & color).
  * Suggestion: show **First + Last name**, styled avatar initial.
* **Text Area**

  * Non-responsive for normal users (ok for super admin).
* **Groups**

  * CEO cannot create group (who is authorized?).
  * Super admin can, but UI buggy and group disappears.
* **Permissions**

  * CEO cannot download chat (403 Access Denied).
* **UI Issues**

  * Resize doesn’t reset on modal close.
  * No settings functionality.
* **Errors**

  * `TypeError: Cannot set properties of undefined (setting 'height')` (ChatModal.vue).
  * WebSocket connection failed.
  * Vue errors with missing routes: `admin.appointments.index`, `admin.medical-records.index`.

---

## 2. **Notifications**

* Notification indicator does not reset after reading.
* Bell dropdown:

  * Cannot close (close icon disappears).
  * Padding collapses.

---

## 3. **RBAC (Roles & Permissions)**

* Yonas (CEO) cannot access patients (403 error).
* Documentation missing: **who can see what**.
* Clinical tools blocked (403), even when user is a doctor.

---

## 4. **Tasks & Leave**

* Creating task: `StoreStaffTaskDelegationRequest` class missing.
* Request leave: `StoreStaffLeaveRequest` class missing.
* Availability insert: invalid class error in BaseController.

---

## 5. **Dashboard**

* Tabs (Analytics, Reports, Notifications): not fetching live DB data.
* Overview cards/tables: static, no DB updates.
* Dark mode: text invisible (black text on dark background).

---

## 6. **Patient Module**

* Create/Edit.vue:

  * Employee dropdown empty.
  * Insurance dropdown empty.
  * Birth date not fetched in edit.
* Print: current print not consistent (should match caregiver).
* Global search not working.

---

## 7. **Service / Visit Services**

* Creation error: **SQLSTATE not null violation** (`is_paid_to_staff`).

---

## 8. **Medical Documents & Prescriptions**

* Create.vue UI inconsistent with Patient/Caregiver.
* File attach (PDF) fails validation.

---

## 9. **Staff Module**

* Export: `_ctx.exportData is not a function`.
* Print Current: shrinks columns, not landscape.
* Responsive: search box overlaps sidebar on minimized screens.

---

## 10. **Staff Payouts**

* Print UI inconsistent with other modules.
* Seeder needed: unpaid & paid example staff payouts.

---

## 11. **Invoices**

* Create.vue: no form to choose invoice.
* Print All: direct download only → should preview in browser first.

---

## 12. **Services Price List**

* No form to create new service.

---

## 13. **Task Delegations**

* Show\.vue / Index.vue: print UI inconsistent with others.

---

## 14. **Leave Requests & Suppliers**

* Index shows nothing (seed/sample data needed).

---

## 15. **Inventory**

* Requests:

  * Error: missing parameter `status` in DTO.
  * Export CSV: `_ctx.exportData is not a function`.
* Maintenance:

  * Status column not colorized.
* Transactions:

  * No Create button or list.
  * Print UI inconsistent.
* Alerts:

  * “Triggered” column missing in create.vue.
  * Empty on index.vue.
  * Print not working.

---

## 16. **Marketing**

* Campaigns:

  * No form to create campaign.
  * Export/Print not working.
* Leads:

  * Show\.vue print not landscape.
* Landing Pages:

  * Form fields JSON unclear.
  * Export CSV broken.
* Platforms / Lead Source:

  * Create.vue inconsistent UI, no labels.
  * Print not working.
* Budgets / Campaign Contents:

  * Cannot test budgets (campaign required).
  * Export CSV fails for campaign contents.
* Tasks:

  * Dropdowns not linked to models.
* Analytics:

  * Data not fetching from DB.
* ROI / Revenue / Service Volume:

  * Seeder needed for sample data.
  * Filters must work.

---

## 17. **Events**

* Create.vue: no form.
* Eligibility / Recommendations / Staff Assignments / Broadcasts:

  * Cannot create (events missing).

---

## 18. **Corporate Clients & Insurance**

* Create/Edit.vue UI inconsistent.
* Insurance Claim:

  * Print current font colors bad (labels bold, not distinct).

---

## 19. **Partners**

* Agreements:

  * Export CSV broken.
  * Print UI inconsistent, not landscape.
* Commissions:

  * Invoice dropdown empty.
* Engagements:

  * Git conflict artifacts (`<<<<<<< HEAD`) visible in UI.
* Referral Documents:

  * Show\.vue broken.
  * Edit.vue doesn’t update.
  * UI inconsistent.

---

## 20. **AppSidebar**

* Communication group → Message/Notification buttons don’t redirect.

---

## 21. **User Management**

* Create/Edit.vue: permissions not selectable.

---

✅ This structure makes it easier for **AI agents or developers** to:

1. **Prioritize** (critical errors first: RBAC, missing classes, SQL issues).
2. **Assign** (UI vs Backend vs DB).
3. **Track** fixes across modules consistently.

---

👉 Do you want me to now **turn this into a proper GitHub Issues template (markdown with checklists)** so your dev team/AI agents can directly import and manage each issue?
