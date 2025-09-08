Mobile API (V1)

Auth
- POST /api/v1/login (Sanctum); POST /logout; /me for profile.

Core Endpoints
- Patients: index/show; self endpoints at /patients/me.
- Visit Services: schedule, create/update, check-in/out.
- Messaging: DMs under /messages/*; groups under /groups/*.
- Notifications: GET /notifications; mark read.
- Push Tokens: POST/DELETE /push-tokens.
- Insurance, Analytics, Bulk Operations: see app/Http/Controllers/Api/V1/*.

Responses
- Use Laravel Resources; include pagination meta and data arrays.

Security
- Sanctum auth; rate limits applied on write endpoints.
