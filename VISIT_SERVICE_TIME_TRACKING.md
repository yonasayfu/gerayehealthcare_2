# Visit Service Time & Location Tracking

This document explains how visit start/end times and locations are captured across mobile and web, how corrections are audited, and how long‑running visits are auto‑completed.

## Overview

- Check‑in captures: time + GPS (latitude/longitude)
- Check‑out captures: time + GPS (latitude/longitude)
- Mobile can send device timestamps; server validates and applies if reasonable
- Web admins can manually adjust times (with optional reason) and GPS
- Long‑running visits are auto‑checked‑out by a scheduler

## Mobile API

Endpoints (all under `/api/v1`, auth: Sanctum):

- `POST /visit-services/{id}/check-in`
  - Body: `latitude` (number), `longitude` (number), `timestamp?` (ISO string)
  - Rules: timestamp is used if not more than +5 minutes ahead and not older than 12 hours; otherwise server time is used.

- `POST /visit-services/{id}/check-out`
  - Body: `latitude` (number), `longitude` (number), `timestamp?` (ISO string)
  - Rules: timestamp is used if not more than +5 minutes ahead and not older than 24 hours; otherwise server time is used. If out < in, out is clamped to in.

- `GET /visit-services/my-schedule` returns upcoming/assigned visits with patient/staff.

Response payload includes: `scheduled_at`, `check_in_time`, `check_out_time`, `check_in_latitude/longitude`, `check_out_latitude/longitude`, and file URLs.

## Web Admin

- Create/Edit forms allow optional manual input for:
  - `check_in_time`, `check_out_time` (datetime‑local)
  - `check_in_latitude/longitude`, `check_out_latitude/longitude`
  - `time_change_reason` (stored in audit when times change)

## Audit Trail

- Table: `visit_service_audits`
  - Columns track before/after values for check‑in/out times, `changed_by_user_id`, and `reason`.
  - Inserted automatically when times are changed via admin.

## Auto‑Checkout Scheduler

- Command: `php artisan visits:auto-checkout --hours=8`
- Default cadence: hourly (configured in `app/Console/Kernel.php`)
- Logic: completes visits left `In Progress` by setting `check_out_time = min(now, check_in + maxHours)` and recalculating earned cost.
- To enable in production: add cron entry
  - `* * * * * php /path/to/artisan schedule:run >> /dev/null 2>&1`

## Configuration

- Change the max hours by passing `--hours` or adjusting your cron entry (e.g., `--hours=10`).
- If you want to require `time_change_reason` whenever times are modified, we can enforce it in validation on request.

## Edge Cases

- Missing check‑in: check‑out clamps to check‑in when computing duration.
- Bad device clock: server ignores unreasonable timestamps.
- Offline mobile: send queued requests later with original `timestamp`; server will accept if within bounds above.
