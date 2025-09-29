# Geraye Healthcare – Mobile MVP Launch Scenario

## 1. Purpose
Create a shared launch plan that keeps the Laravel web platform and the Flutter mobile client in sync, highlights must-fix gaps, and outlines the fastest/safest path to an MVP release that works for staff and admin roles across backend, web, and mobile.

## 2. Current Snapshot

### Backend & Web Admin
- Mature API surface with Sanctum auth, role-aware middleware, and module discovery (`/api/v1/login`, `/api/v1/modules`, `/api/v1/visit-services/*`) (`routes/api.php:22`, `routes/api.php:31`, `routes/api.php:50`).
- RBAC matrix and module metadata are centralised and already consumed by the web UI (`app/Support/ModuleAccess.php:21`, `MD/GROUP_2_SECURITY_RBAC/RBAC_ROLE_ACCESS_MATRIX.md:1`).

### Flutter Mobile App
- Clean architecture, Riverpod DI, rich UI system, messaging, settings, and notification infrastructure implemented (`MD/GROUP_3_MOBILE_API/FLUTTER_DEVELOPMENT_PROGRESS_UPDATED.md:15`, `gerayehealthcare-mobile-app/lib/core/notifications/notification_service.dart:61`).
- Notification provider and local persistence already wired even though documentation still lists Phase 7 as pending (`gerayehealthcare-mobile-app/lib/presentation/providers/notification_provider.dart:52`, `MD/GROUP_3_MOBILE_API/FLUTTER_DEVELOPMENT_PROGRESS_UPDATED.md:172`).

### Divergences to Resolve
- **API contract mismatch** – Laravel responses return snake_case fields without a `success` envelope (`app/Http/Controllers/Api/V1/AuthController.php:32`), while the mobile data sources expect a `{ success: true, data: … }` payload and camelCase tokens (`gerayehealthcare-mobile-app/lib/data/datasources/auth_remote_datasource.dart:34`, `gerayehealthcare-mobile-app/lib/data/models/user_model.dart:81`).
- **Unused endpoints** – Mobile constants point to `/auth/*` routes (`gerayehealthcare-mobile-app/lib/core/constants/api_endpoints.dart:7`), but the API exposes `/login`, `/logout`, and `/user` without the `/auth` prefix (`routes/api.php:22`, `routes/api.php:25`, `routes/api.php:27`).
- **Role/module data not yet consumed in Flutter** – `GET /modules` and `UserResource` expose role-based modules (`app/Http/Controllers/Api/V1/UserController.php:21`, `app/Support/ModuleAccess.php:21`), but there is no parsing of that metadata in the Dart domain entities yet (`gerayehealthcare-mobile-app/lib/domain/entities/user.dart:6`).
- **Testing gaps** – Web has PHPUnit suites; Flutter currently ships only the generated widget smoke test (`gerayehealthcare-mobile-app/test/widget_test.dart:1`).

## 3. MVP Scope by Persona
- **Staff (field caregivers):** authenticate, view assigned visits, check-in/out with GPS, message supervisors/patients, receive push notifications for schedule changes, access personal documents and earnings snapshots.
- **Admin / COO:** review today’s KPIs, manage staff roster and visit assignments, approve insurance/claims read-only, triage marketing leads, monitor notifications and messaging escalations.
- **CEO/Executive:** mostly view dashboards, analytics, and high-level reports; minimal data mutation on mobile.

## 4. Workstreams to Hit MVP

1. **Contract Alignment (Blocker)**
   - Adopt one contract: either wrap Laravel responses in `{success, data}` or relax Flutter expectations. Easiest short-term win is to update the Retrofit/Dio layers to accept Laravel’s current shape (snake_case, no `success`).
   - Actions: adjust `ApiEndpoints` to match `/api/v1/*`; update serializers to use `@JsonKey(name: 'access_token')`; remove `success` checks in repositories.

2. **Role-Aware Navigation**
   - Parse `modules` payload into a lightweight domain object, drive the Flutter navigation shell from it, and enforce permission-aware UI similar to the Vue sidebar.
   - Actions: extend `UserModel` with `modules`, introduce a `ModuleAccess` mapper, and hydrate dashboards/lists based on role.

3. **Messaging & Notifications Completion**
   - Wire FCM token registration to the `/push-tokens` endpoints (`routes/api.php:167`) and confirm background handlers sync read receipts with `/notifications/{id}/read` (`routes/api.php:163`).
   - Update documentation to reflect the existing NotificationService work and finish any remaining UI polish (filters, badge counts).

4. **Offline & Sync Checks**
   - Validate background sync services, cache invalidation, and error recovery. Add instrumentation to monitor failed syncs.
   - Ensure visit completion and document upload flows handle retries gracefully.

5. **QA & Observability**
   - Add golden tests for key dashboards, API integration tests for auth + visit flows, and flutter_driver/integration tests covering staff check-in/out.
   - Prepare Sentry/Crashlytics and Laravel logging dashboards for post-launch monitoring.

## 5. Launch Scenario Options

| Scenario | Pros | Cons |
| --- | --- | --- |
| **Launch immediately after smoke tests** | Fast stakeholder demo; validates UI/UX decisions | High risk of 401/500s due to contract mismatches; staff can’t complete visits; rollback likely |
| **Fix contract + module parity first (recommended MVP)** | Ensures staff/admin flows match web; minimal backend churn; validates RBAC consistency | Requires coordinated sprint (~1–1.5 weeks), delaying demo slightly |
| **Full parity + test automation before launch** | Highest confidence for production rollout; cleaner handover to operations | Longer runway (2–3 weeks); higher opportunity cost if urgent market launch |

## 6. Launch Checklist

### Backend / API
- [ ] Expose consistent response format (or document and mirror existing format) for auth, users, visits, notifications (`app/Http/Controllers/Api/V1/AuthController.php:32`, `app/Http/Controllers/Api/V1/UserController.php:21`, `routes/api.php:50`).
- [ ] Seed roles & permissions and cache-reset before tagging release (`database/seeders/RolesAndPermissionsSeeder.php`).
- [ ] Enable `/push-tokens` endpoints and verify they store multiple device tokens per user (`routes/api.php:167`).

### Flutter Mobile
- [ ] Update `ApiEndpoints` base paths and remove stale `/auth/*` routes (`gerayehealthcare-mobile-app/lib/core/constants/api_endpoints.dart:7`).
- [ ] Map server tokens & modules into domain models (`gerayehealthcare-mobile-app/lib/data/models/user_model.dart:81`).
- [ ] Implement API client smoke tests (login, fetch visits, messaging) and widget snapshots for dashboards (`gerayehealthcare-mobile-app/test/widget_test.dart:1`).
- [ ] Configure FCM, push token registration, and background handlers using the existing service scaffold (`gerayehealthcare-mobile-app/lib/core/notifications/notification_service.dart:61`).

### Joint QA
- [ ] Run role-based UAT scripts covering staff visit lifecycle, admin staff management, CEO dashboards (reuse flows documented in `MD/GROUP_4_USER_GUIDES/GERAYE_HEALTHCARE_COMPLETE_USER_GUIDE.md`).
- [ ] Verify localization, timezones, and Ethiopian/Gregorian conversions via `DateConversionController` endpoints (`routes/api.php:201`).
- [ ] Execute end-to-end messaging tests (direct + group) ensuring read receipts update across platforms (`routes/api.php:80`, `routes/api.php:132`).

### Deployment
- [ ] Provision staging Firebase project and .env for mobile + backend push channels.
- [ ] Tag Docker/Vite builds and align environment configs (`render.yaml`, `vite.config.ts`).
- [ ] Prepare app store listings and internal testing tracks.

## 7. Git & Repository Strategy
- Keep backend and mobile repos cleanly separated to avoid conflicting histories. Either move the Flutter app to its own repository or ignore it in the Laravel repo (add `gerayehealthcare-mobile-app/` to `.gitignore` alongside existing local references like `Messaging-APP/`).
- If both live in one repo temporarily, pin a `README` pointer explaining the authoritative repo for each app and avoid cross-commits.
- Use consistent release branches (`backend/release/x.y`, `mobile/release/x.y`) to coordinate MVP tags.

## 8. Suggested Schedule (10-day Sprint)
1. **Days 1–2:** Contract alignment (Flutter serializers + endpoint constants) and backend response confirmation.
2. **Days 3–4:** Module-based navigation + role-scoped dashboards.
3. **Days 5–6:** Push token wiring, notification polish, visit/messaging smoke automation.
4. **Days 7–8:** Joint UAT + bug bash, finalize documentation.
5. **Days 9–10:** Release packaging, store submissions, handover checklist.

## 9. Risks & Mitigations
- **Integration drift:** Lock API response schemas via OpenAPI or Schema tests; add regression tests before future backend refactors.
- **Role leakage:** Use the existing permission matrix during QA; monitor `/api/v1/modules` to ensure mobile surface matches RBAC rules.
- **Offline edge cases:** Add analytics around sync failures and store-and-forward queues so operations can monitor field reliability.

---
Use this scenario as the living guide—update it after each milestone so both teams stay aligned through MVP launch and beyond.
