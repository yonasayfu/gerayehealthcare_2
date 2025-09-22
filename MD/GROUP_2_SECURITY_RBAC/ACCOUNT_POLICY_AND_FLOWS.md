# Account Policy and Flows (Option B)

This document describes how accounts are created and managed when self‑registration is allowed only for patients/guests, while employees are provisioned by administrators.

## Roles & Personas

- Super Admin / Admin: Full management of users, roles, staff, and invitations.
- Employee (Staff: doctors, nurses, operations, finance, etc.): Have a Staff profile and a linked User account with appropriate roles. Provisioned by Admins.
- Patient/Guest: Minimal User account for viewing bills, results, messages, self‑service features. May self‑register.

## Policy Summary

- Self‑registration: Enabled only for patients/guests.
- Employee accounts: Admin‑provisioned (no public sign‑up). Admin assigns roles.
- Security: Email verification recommended; enforce strong passwords. 2FA strongly recommended for Admin/Staff.

## Web/Mobile Registration (Patients/Guests)

1. User opens Register and submits name, email, password.
2. System creates a `User` with the `patient` role and a linked `Patient` profile (full_name, email).
3. The user is logged in and redirected to the dashboard.

Notes:
- Patients may later add phone, address, DOB, etc.
- If a Patient later becomes an employee, Admin creates/links a Staff profile and assigns staff roles; existing patient role may remain if needed.

## Employee Provisioning (Admins)

There are two supported admin flows:

- From Staff module:
  1. Create Staff profile (name, department, email).
  2. "Create login" action auto‑creates a `User` for this Staff and assigns the chosen role(s).
  3. Optionally send an invite email with a password‑set link.

- From Users module:
  1. Create `User` and assign roles.
  2. Link to an existing Staff profile or create one inline.

## Permissions Overview

- Patients/Guests: restricted to self‑service (no admin features).
- Staff: roles assigned via Spatie; least privilege per department (e.g., caregiver, finance, marketing).
- Admins: manage users, roles, and sensitive data.

## Operational Guidance

- Disable or guard staff self‑registration paths; ensure the Register page and APIs create only patient/guest users.
- Keep role assignment centralized in Admin/Users to prevent privilege sprawl.
- Periodically review role assignments and 2FA status.

## Change Log

- Register controller now assigns `patient` role and creates a linked `Patient` model automatically on self‑registration.
- Register page clarifies that employees are invited by admins; patients/guests register here.
