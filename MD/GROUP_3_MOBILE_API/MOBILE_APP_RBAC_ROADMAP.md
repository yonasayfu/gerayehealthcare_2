# Geraye Healthcare Mobile — RBAC Alignment Roadmap

## 1. Objectives
- Deliver a focused Android/iOS experience for four mobile personas: **Super Admin**, **Doctor**, **Nurse**, and **Patient**.
- Redirect all other organisational roles (finance, HR, marketing, operations, etc.) to the web console where the full back-office tooling lives.
- Keep mobile role detection in sync with the Laravel API via the `/me` endpoint (roles, permissions, module list) so no hardcoded access matrices drift over time.

## 2. Persona Definitions
| Persona | Detection Logic | Primary Goals |
| --- | --- | --- |
| Super Admin | `roles` includes `super-admin` | Full control, rapid status checks, ability to impersonate web experience if needed |
| Doctor | Role includes `doctor` **or** modules contain `clinical`/`tasking` permissions | Manage schedule, complete visits, update clinical notes/prescriptions, communicate with patients and team |
| Nurse | Role includes `nurse` **or** inherits clinical/tasking modules with limited authoring permissions | Similar to doctor with emphasis on visit execution and checklists |
| Patient | Role includes `patient` **or** permissions limited to self-service + communications | Track upcoming visits, view visit history, exchange secure messages, receive alerts |

> When the API returns module sets that do not match any persona, the app now directs the user to the “Mobile Access Not Available” screen and invites them to continue on the web.

## 3. Mobile Experience by Persona
### Super Admin
- Global dashboard widgets (KPI, staffing, incidents)
- Operational modules (patients, staff, assignments, inventory, marketing, insurance)
- Power tools (notifications, messaging, settings, profile)

### Doctor / Nurse
- “Today’s Overview” card with visits & key alerts
- My Visits (check-in/out, document notes, GPS metadata)
- Patient roster shortcuts filtered to assigned caseload
- Assignments / to-do board integration
- Secure messaging & push notifications

### Patient
- Simplified “My Visits” timeline (upcoming, in-progress, completed)
- Messaging & alerts channel to care team
- Profile & settings with personal info, device settings, language preference

## 4. Implementation Checklist
- [x] Parse `modules`, `roles`, and `permissions` from `/api/v1/me`
- [x] Persona classification + GoRouter guards
- [x] Persona-specific home navigation + module chips
- [x] Unsupported-role screen + logout workflow
- [ ] Clinician visit list wired to real API endpoints
- [ ] Patient self-service endpoints (profile, visit history, invoices)
- [ ] Role-based empty states & skeleton loaders
- [ ] Analytics: persona adoption metrics & crash funnels

## 5. Sprint Plan (Suggested)
1. **API Wiring (2-3 days)** — hook clinician visit list & patient self endpoints to live Laravel routes, add repository methods still returning `UnimplementedError`.
2. **Clinician UX Polish (2 days)** — offline state cues, quick actions (check-in/out, add note), visit detail parity with web.
3. **Patient Journey (2 days)** — simplified visit cards, prescription viewer, emergency contact tile.
4. **Role QA & Regression (1 day)** — test matrix across sample accounts (super-admin, doctor, nurse, patient) + ensure unsupported roles see web-only message.
5. **Observability (1 day)** — add analytics events (`persona_detected`, `unsupported_role_redirect`, `visit_complete_mobile`) plus Crashlytics breadcrumbs.

## 6. Risk & Mitigation
- **Backend Role Drift** — document any new role slugs in `mobile_access_control.dart` and update seeders accordingly.
- **Staff Position Metadata** — if positions (doctor/nurse) move from RBAC to profile metadata, expose to `/me` so the app can classify without guesswork.
- **Patient Permissions** — confirm patients receive self-service permissions (`patients/me`, `notifications`) in Laravel to keep mobile usable.

## 7. Communication Plan
- Weekly sync with backend/web leads to validate new permissions before release.
- Include persona coverage in release notes (“v1 supports Super Admin, Doctor, Nurse, Patient”).
- Maintain this roadmap alongside the MVP execution plan to keep Android focus clear.
