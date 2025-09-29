# Geraye Healthcare Mobile – Execution Plan

This plan reflects the recommended "contract + module parity first" path so the Android MVP launches with stable foundations. Items already completed are checked off; future work stays open for easy tracking.

## 1. Alignment & Planning
- [x] Deliver shared MVP launch scenario and checklist (`MOBILE_APP_MVP_SCENARIO.md`).
- [ ] Review plan with stakeholders and lock scope for the next sprint.

## 2. API Contract & Data Layer
- [ ] Update Flutter `ApiEndpoints` to match current Laravel routes (`/api/v1/login`, `/api/v1/logout`, `/api/v1/user`, etc.).
- [ ] Adjust Dio/Retrofit models to consume Laravel’s snake_case responses (map `access_token`, `token_type`, `modules`, etc.).
- [ ] Add regression unit tests covering login, `/me`, `/visit-services/my-schedule`, and `/notifications`.

## 3. Role-Aware Experience
- [ ] Extend `UserModel`/domain entities to parse `modules` and permission summaries.
- [ ] Drive navigation and dashboard widgets from module metadata so Staff/Admin/CEO get scoped menus.
- [ ] Add UI smoke tests that assert visibility/hidden state for restricted modules.

## 4. Messaging & Notifications
- [ ] Wire FCM token registration to `/api/v1/push-tokens` and handle refresh lifecycle.
- [ ] Ensure background handlers sync read receipts via `/api/v1/notifications/{id}/read`.
- [ ] Document the updated notification flow and mark Phase 7 as complete in progress docs.

## 5. Android-Focused Delivery
- [ ] Configure Firebase Android app (google-services.json, notification channels, icon assets).
- [ ] Validate background services, WorkManager, and offline queues on Android emulators and physical devices.
- [ ] Prepare build flavors (dev/staging/prod) and signing configs for Play Console tracks.
- [ ] Run performance profiling (jank stats, memory footprint) on mid-range Android hardware.

## 6. QA & Release Readiness
- [ ] Add integration tests (flutter_test / integration_test) covering staff visit lifecycle and messaging threads.
- [ ] Conduct role-based UAT and capture sign-off checklists for Staff, Admin, CEO personas.
- [ ] Finalize release notes, crash reporting (Sentry/Crashlytics), and Play Store listing assets.

---
Update this file as tasks progress so everyone can see the Android MVP status at a glance.
