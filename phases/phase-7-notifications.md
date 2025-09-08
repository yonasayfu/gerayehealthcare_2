# Phase 7 — Notifications, Email, Logs & Audit

> Architecture alignment: Services emit events; listeners send email/in-app notifications; UI shows audit trails.

Ensure robust notifications and an auditable log UI for Super Admin.

---

- [x] **System logging & audit UI** (prio:P2)  
  *Task:* Confirm logging channel; build admin audit page to filter who/what/when.  
  *Ref:* (UI path to be created), Laravel `config/logging.php`

- [ ] **Auth & password reset – prod ready** (prio:P1)  
  *Task:* Wire real mailer; keep dev fake path for local.  
  *Ref:*  
    `/Users/yonassayfu/VSProject/gerayehealthcare/resources/js/pages/auth/*`  
    `/Users/yonassayfu/VSProject/gerayehealthcare/app/Http/Requests/Settings`  

- [x] **In-app vs email parity** (prio:P2)  
  *Task:* Mirror critical events to bell + email with user preferences.  
  *Ref:*  
    `/Users/yonassayfu/VSProject/gerayehealthcare/app/Notifications/*`  
    `/Users/yonassayfu/VSProject/gerayehealthcare/app/Providers/EventServiceProvider.php`

---

### Clean Architecture Alignment
- Keep notification/email triggers in services; controllers stay orchestration-only.

### OriginalIssue Traceability
- Notifications (caregiver assignment, task delegation): items 95, 38
- Insurance claim email pipeline: item 96
- Auth & password reset with local fake + prod readiness: items 34, 100
- Event broadcasts/participants/recommendations alerts: items 70–72

## Commit & Push
1. `git checkout -b fix/phase-7-notifications-logs`  
2. Commit: `feat(notify): bell+email parity and audit log UI`  
3. `git push origin fix/phase-7-notifications-logs`

**Reminder:** Add screenshots of audit UI filters + example notification flow.
