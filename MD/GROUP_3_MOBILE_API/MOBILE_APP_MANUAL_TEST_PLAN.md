# Geraye Healthcare Mobile — Manual Test Plan (Mac M1)

## 1. Prerequisites
- **Backend**: Laravel API running locally (default `php artisan serve --host=127.0.0.1 --port=8000`).
  - Ensure `.env` has `SANCTUM_STATEFUL_DOMAINS=127.0.0.1:8000` and migration/seed run (`php artisan migrate --seed`).


- **Mobile**: Flutter >=3.0 SDK installed, iOS/Android emulator (or physical device) configured.
  - Ensure `pub get` and `flutter pub run build_runner build` run before testing.

## 2. Test Cases

### 2.1. Login
- **Test Case**: Login with valid credentials
  - **Steps**:
    1. Enter valid email and password
    2. Click on the login button
  - **Expected Result**: User is logged in successfully and redirected to the dashboard


- **Test Case**: Login with invalid credentials
  - **Steps**:
    1. Enter invalid email and password
    2. Click on the login button

- **Accounts**: seeded credentials
  - Super Admin: `superadmin@gerayehealthcare.com / SuperAdmin123!`
  - Doctor: `doctor@gerayehealthcare.com / Doctor123!`
 - Nurse: `nurse@gerayehealthcare.com / Nurse123!`
  - Patient: `patient@gerayehealthcare.com / Patient123!`

### 2.2. Dashboard
- **Test Case**: Dashboard visibility
  - **Steps**:
    1. Login with valid credentials
    2. Verify that the dashboard is visible
  - **Expected Result**: Dashboard is visible with appropriate data

### 2.3. Messaging
- **Test Case**: Send message
  - **Steps**:
    1. Login with valid credentials
    2. Click on the messaging tab
    3. Select a recipient
    4. Enter a message
    5. Click on the send button
- **Accounts**: seeded credentials
  - Super Admin: `superadmin@gerayehealthcare.com / SuperAdmin123!`
  - Doctor: `doctor@gerayehealthcare.com / Doctor123!`
 - Nurse: `nurse@gerayehealthcare.com / Nurse123!`
  - Patient: `patient@gerayehealthcare.com / Patient123!`


| Persona | Focus Areas |
| --- | --- |
| Super Admin | Dashboards, patient/staff management, messaging, notifications |
| Doctor | My visits, patient roster, messaging, check-in/out |
| Nurse | My visits, assignments, messaging |
| Patient | Visit timeline, messaging, notifications |

Tick boxes in the tracker once each persona scenario has been exercised during regression.





## 2. Environment Setup
1. Start Laravel backend:
   ```bash
   cd /Users/yonassayfu/VSProject/gerayehealthcare
   php artisan serve --host=127.0.0.1 --port=8000
   ```


2. Launch Flutter app (Android example):
   ```bash
   cd /Users/yonassayfu/VSProject/gerayehealthcare/gerayehealthcare-mobile-app
   flutter run -d android
   ```
   > For iOS simulator: `flutter run -d ios`



3. Confirm app boots to the login screen without errors.



## 2.1 API Direct Test (cURL)
Use these commands to validate the backend quickly.

1) Health Check
```bash
curl -s http://127.0.0.1:8000/api/v1/system/health | jq
```

2) Login (get token)
```bash
TOKEN=$(curl -s -X POST \
  -H 'Content-Type: application/json' \
  -d '{"email":"doctor@gerayehealthcare.com","password":"password"}' \
  http://127.0.0.1:8000/api/v1/login | jq -r .access_token)
echo $TOKEN
```

3) Me + Modules
```bash
curl -s -H "Authorization: Bearer $TOKEN" http://127.0.0.1:8000/api/v1/me | jq
```




4) Notifications & Preferences
```bash
curl -s -H "Authorization: Bearer $TOKEN" http://127.0.0.1:8000/api/v1/notifications | jq
curl -s -H "Authorization: Bearer $TOKEN" http://127.0.0.1:8000/api/v1/notifications/preferences | jq
curl -s -X PUT -H 'Content-Type: application/json' -H "Authorization: Bearer $TOKEN" \
  -d '{"enable_push":true,"enable_email":false}' \
  http://127.0.0.1:8000/api/v1/notifications/preferences | jq
```


5) Visit Check-in / Check-out (replace :id)
```bash
VISIT_ID=1
curl -s -X POST -H 'Content-Type: application/json' -H "Authorization: Bearer $TOKEN" \
  -d '{"latitude":0.0,"longitude":0.0,"timestamp":"'"$(date -u +%Y-%m-%dT%H:%M:%SZ)"'"}' \
  http://127.0.0.1:8000/api/v1/visit-services/$VISIT_ID/check-in | jq

curl -s -X POST -H 'Content-Type: application/json' -H "Authorization: Bearer $TOKEN" \
  -d '{"latitude":0.0,"longitude":0.0,"timestamp":"'"$(date -u +%Y-%m-%dT%H:%M:%SZ)"'"}' \
  http://127.0.0.1:8000/api/v1/visit-services/$VISIT_ID/check-out | jq
```



6) Patient Self-Service (as patient user)
```bash
PTOKEN=$(curl -s -X POST -H 'Content-Type: application/json' \
  -d '{"email":"patient@gerayehealthcare.com","password":"password"}' \
  http://127.0.0.1:8000/api/v1/login | jq -r .access_token)
curl -s -H "Authorization: Bearer $PTOKEN" http://127.0.0.1:8000/api/v1/patients/me | jq
curl -s -H "Authorization: Bearer $PTOKEN" http://127.0.0.1:8000/api/v1/documents/my | jq
curl -s -H "Authorization: Bearer $PTOKEN" http://127.0.0.1:8000/api/v1/invoices/my | jq
```



7) Push Token Register / Unregister
```bash
curl -s -X POST -H 'Content-Type: application/json' -H "Authorization: Bearer $TOKEN" \
  -d '{"token":"FAKE_DEBUG_TOKEN","platform":"android","device_name":"emulator"}' \
  http://127.0.0.1:8000/api/v1/push-tokens | jq
curl -s -X DELETE -H 'Content-Type: application/json' -H "Authorization: Bearer $TOKEN" \
  -d '{"token":"FAKE_DEBUG_TOKEN"}' \
  http://127.0.0.1:8000/api/v1/push-tokens -w "\n%{http_code}\n"
```




## 3. Persona Scenarios
### 3.1 Super Admin Smoke Test
1. Login as Super Admin.
2. Verify landing page shows the "Super Admin" banner and tiles for Dashboards, Patients, Visits, Staff, Assignments, Inventory, Financials, Marketing, Insurance, Messages, Notifications, Profile, Settings.
3. Navigate into Dashboard, Patients, Staff; confirm no "Access Denied" redirect.
4. Logout and confirm FCM token unregisters (no error toast).

### 3.2 Doctor Workflow
1. Login as Doctor.
2. Ensure persona badge displays "Doctor" and only clinician tiles (Visits, Patients, Assignments, Messages, Notifications, Profile, Settings).
3. Open Visits; verify list populates. (If API not wired yet, note placeholder and mark tracker.)
4. Trigger Messages and Notifications to confirm access.
5. Logout; ensure redirect to login.

### 3.3 Nurse Workflow
1. Login as Nurse account.
2. Confirm navigation identical to Doctor persona (Visits-focused) with "Nurse" banner.
3. Validate messaging works.
4. Logout.

### 3.4 Patient Journey
1. Login as Patient.
2. Verify persona shows "Patient" and only My Visits, Messages, Notifications, Profile, Settings tiles.
3. Open each tile to confirm accessible (Visits should show self-focused data once API wired).
4. Logout.

### 3.4.1 Patient Self-Service
1. Login as Patient.
2. Open "My Documents"; verify your documents (prescriptions, doctor notes, lab) list or empty state.
3. Open "My Invoices"; verify invoices list with status/total.
4. Attempt to access another patient’s document by ID (should be blocked). Download remains restricted by policy.

## 2.2 Queue & Scheduler (Optional)
- For local queue processing: `php artisan queue:work --tries=3 --sleep=3`
- Scheduler (cron) to run tasks like auto-checkout: `* * * * * php artisan schedule:run`

### 3.5 Unsupported Role Guard
1. Temporarily assign `admin@...` to a role outside the four personas (e.g., `admin`).
2. Login; verify redirect to "Mobile Access Not Available" screen with guidance.
3. Use "Sign out" button; confirm session clears.

## 4. Notifications
1. With backend running, log in as Doctor on Android emulator (FCM requires Google Services in debug or emulator with Play services).
2. Inspect backend DB `push_tokens` table to confirm token entry created for user.
3. Logout; ensure token row removed.

## 4.1 Notification Preferences
1. Open Settings -> Notifications.
2. Toggle "Notifications" off; verify "Push" and "Email" toggles become disabled in UI.
3. Toggle "Notifications" on; enable "Push" and/or "Email" and observe immediate local state update.
4. Verify backend persists preferences:
   - GET `GET /api/v1/notifications/preferences` returns the updated flags (`enable_push`, `enable_email`, `enable_notifications`).
5. Switch Notification Frequency and verify UI reflects the choice and an analytics event is logged in console.

## 6. Clinician Offline Check-in/Out
1. As Doctor/Nurse, navigate to a Scheduled visit.
2. Disable network (Airplane mode or disable emulator network).
3. Perform Check-in:
   - App shows "Check-in queued for sync".
   - A small "Pending sync" badge appears on the visit card.
4. Re-enable network; wait up to 30s for background sync or manually trigger sync from the sync controls.
5. Confirm the visit updates to In Progress and the pending badge disappears.
6. Repeat steps for Check-out; confirm queued state and eventual completion.

## 5. Regression Checklist
- [ ] Splash -> Login -> Persona redirect flows with/without auth token
- [ ] Back navigation from secondary screens returns to persona home
- [ ] Unsupported roles never show main navigation
- [ ] Role chips update if backend role changes (log out/in to refresh)
- [ ] No persona sees tiles outside their scope

## 6. Troubleshooting
- If you see network 401s, confirm Sanctum cookies and app base URL (`ApiConstants.baseUrl`) point to the same host/port as Laravel.
- For FCM errors on iOS simulator, remember Apple simulators don’t receive push tokens; the app still stores a placeholder.
- Use `flutter logs` while cycling personas to ensure no unexpected exceptions surface.

Update this checklist as additional endpoints are wired (visit CRUD, patient invoices, documents), and confirm patient self-service `/patients/me` profile loads on the Profile screen.
