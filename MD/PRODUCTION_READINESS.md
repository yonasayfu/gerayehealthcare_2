# Geraye Healthcare — Production Readiness

## 1) Environment Configuration (Backend)
- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL=https://<your-domain>`
- Database: `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
- Queues: `QUEUE_CONNECTION=database` (or `redis`) and run workers
- Mail: provider credentials (SendGrid/Mailgun)
- Files: `FILESYSTEM_DISK` as needed (`public`/`s3`) and `php artisan storage:link`
- Proxy: `TRUSTED_PROXIES=*` (or your ingress CIDRs)
- Sanctum (if SPA): `SANCTUM_STATEFUL_DOMAINS=<web hosts>`

## 2) Health Check
- `GET /api/v1/system/health` → `{ status: "ok", env, app, version, time }`
  - Use this for uptime monitors / load balancers.

## 3) Build & Optimize
Run after deploy (CI/CD or once on boot):
- `php artisan migrate --force`
- `php artisan config:cache`
- `php artisan route:cache`
- `php artisan view:cache`
- `php artisan event:cache`
- `php artisan storage:link`

## 4) Queues & Scheduler
- Start queue workers (Supervisor/PM2/systemd): `php artisan queue:work --sleep=3 --tries=3`
- Add a scheduler (cron): `* * * * * php /path/to/artisan schedule:run >> /dev/null 2>&1`

## 5) RBAC Alignment
- Seed roles & permissions: `php artisan db:seed --class=RolesAndPermissionsSeeder`
- Test users seeded via `TestUsersSeeder` with staff entries and permissions for visit flows.
- Mobile personas resolve to:
  - Super Admin → `super-admin`
  - Doctor/Nurse → inferred from `staff.position` ("Doctor"/"Nurse") or clinical permissions
  - Patient → inferred via self endpoints/modules

## 6) Mobile App Configuration
- Set API base URL in app constants for production builds
  - `--dart-define=ENVIRONMENT=production` and point baseUrl to your API host
- Verify push token registration (`/api/v1/push-tokens`) and preferences (`/api/v1/notifications/preferences`)

## 7) Smoke Checklist
- Auth: login/logout, `/api/v1/me`
- Visits: list, detail, check-in/out (online), then offline queue → sync
- Notifications: list, mark read, preferences
- Documents/Invoices: `/documents/my`, `/invoices/my` (read-only)
- Health check returns `ok`

## 8) Logging & Monitoring
- `LOG_CHANNEL=stack`, `LOG_LEVEL=info`
- Enable APM/metrics in your hosting (Render/AWS) and set monitors on health endpoint.

