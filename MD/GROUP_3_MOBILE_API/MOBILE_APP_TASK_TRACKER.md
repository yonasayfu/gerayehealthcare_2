# Geraye Healthcare Mobile — RBAC Execution Tracker

## API & Data Layer
- [x] Align auth endpoints and token parsing with Laravel responses
- [x] Remove `{success}` response assumptions from remaining Dio data sources

## Personas & Navigation
- [x] Classify super-admin / doctor / nurse / patient via `/me` metadata
- [x] Redirect unsupported roles to dedicated guidance screen
- [x] Tailor home navigation tiles per persona

## Notifications
- [x] Sync FCM tokens to `/api/v1/push-tokens`
- [x] Unregister tokens on logout
- [x] Surface notification history via API
- [x] Expose notification preference toggles in Settings

## Clinician Journey
- [x] Wire visit list and detail views to live API endpoints
- [x] Provide offline-safe check-in / check-out interactions

## Patient Journey
- [x] Display personal visit timeline and alerts
- [x] Expose self-service profile view

## Observability & QA
- [x] Emit persona & navigation analytics events
- [x] Maintain role-based manual test matrix

> Update checkboxes as you land each deliverable to keep the mobile MVP scope transparent.
