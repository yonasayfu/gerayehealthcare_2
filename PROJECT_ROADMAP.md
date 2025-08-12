# Project Roadmap & Vision: Home-to-Home Care Platform

This document outlines the vision, architecture, features, and technology stack for the Home-to-Home Care Platform.

## 0. Execution Plan with Web-Chat AI Agents

To execute issues via browser-based AI agents with limited project access, use a consistent “context pack” for every task:

- **Context Pack (always share):**
  - PROJECT_ROADMAP.md (this file)
  - DATABASE_SCHEMA.md
  - ISSUE_TRACKER.md (filtered to the target issue)
  - Only the directly related code for the issue: controller(s), model(s), service(s), Vue pages/components, routes, and any config used

- **Issue Brief Template (paste into chat):**
  - Title: <clear, single-scope change>
  - Module: <Patients | Staff | Inventory | ...>
  - Current behavior: <what happens now>
  - Expected behavior: <what you want>
  - Files provided: <list>
  - Routes/UI entry points: <route names + Vue paths>
  - Constraints: <tech constraints, coding standards>
  - Done criteria: <acceptance checks>

- **Sharing strategy:**
  - Start with the 3 docs + the minimal set of files needed.
  - If the agent asks for more, share only the requested file(s).
  - Keep issues small; avoid multi-module changes in a single chat.

## Sprint Plan (90-day outline)

- **Sprint 1 (Weeks 1–3): Phase 1, Modules 1–4**
  - Stabilize Staff, Patients, Caregiver Assignments, Visit Services.
  - Target: close all “blocking” and “data integrity” issues first.

- **Sprint 2 (Weeks 4–6): Phase 2, Modules 5–12**
  - Admin & Inventory reliability. Seeders for reproducible testing.

- **Sprint 3 (Weeks 7–9): Phase 2 cont. + Phase 3 (start)**
  - Transactions/Alerts polish; begin Marketing Campaigns/Leads.

- **Sprint 4 (Weeks 10–12): Phase 3 wrap-up**
  - Complete Marketing modules and Events.

- **Sprint 5 (Weeks 13–15): Phase 4**
  - Insurance + User/Role management, UX consistency pass.

Link to details: see ISSUE_TRACKER.md for per-module tasks and priorities.

## 1. System Architecture (High-Level)

- **🗂️ Backend: Laravel 12 (RESTful API + Background Jobs)**
  - **Authentication**: Sanctum
  - **Queue System**: Laravel Queues with Redis
  - **Storage**: Local + S3
  - **Notifications**: Email, SMS, Push (via Firebase for mobile)
  - **Scheduler**: Laravel Task Scheduler
  - **API**: RESTful endpoints for Flutter mobile apps

- **🎨 Frontend: Vue 3 (with Inertia.js for SPA behavior)**
  - Admin panel
  - Doctor dashboard
  - Patient portal

- **📱 Mobile App: Flutter (Future Phase)**
  - Patient app for booking, monitoring
  - Staff app for on-field service delivery
  - Connects to Laravel API
  - Push notifications via Firebase

- **🛢️ Database: PostgreSQL**
  - Modular schemas: patients, services, referrals, inventory, etc.

- **🔌 3rd-Party Integrations**
  - TikTok Ads API (Marketing ROI tracking)
  - Insurance APIs (claims sync, coverage status)
  - Hospital APIs (referral handling)
  - Google Maps (location & routing)
  - Email/SMS providers

## 2. Core Feature Modules

### ✅ Implemented

1.  **Patient Management**
    - Patient registration and medical profile
    - Medical history tracking
    - Appointment scheduling
    - Emergency contact info
    - Geolocation/address tracking for home visits

2.  **Caregiver/Nurse Management**
    - Register caregivers (nurses, physical therapists, etc.)
    - Assign caregivers to patients
    - Shift scheduling and availability
    - Performance and feedback system
    - Certification and training records

3.  **Admin & HR Tools**
    - Dashboard for organization overview
    - Staff management and leave requests
    - Task delegation

4.  **Digital Marketing Tracker**
    - Campaign tracker (TikTok, Meta, etc.)
    - Custom landing page generator
    - Conversion tracking
    - Monthly ROI report

5.  **Inventory Management**
    - Track rented/loaned equipment
    - Inventory status and location
    - Alerts for expiry, maintenance, or recall

6.  **Events Management**
    - Winter/monthly free service scheduler
    - Broadcast to partners & public
    - Volunteer doctor assignment

7.  **Insurance Coordination**
    - Check coverage on patient registration
    - Sync claims for services
    - Track shared payments
    - Notifications for payment delays/denials

### 🚧 In Progress

1.  **Visit & Service Management**
    - Daily visit schedule for caregivers
    - Check-in/check-out via GPS (proof of visit)
    - Digital notes and reports from each visit
    - Upload prescriptions, vitals, and images

### 📝 To Do

1.  **Communication**
    - Secure in-app chat between staff and patients
    - Notification system (reminders, updates)
    - Video consultation option

2.  **Billing & Payments**
    - Invoice generation
    - Service pricing packages
    - Payment gateway integration
    - Receipt history

3.  **Medical Records & Documentation**
    - Digital prescriptions
    - Lab result uploads
    - Progress notes and treatment plans

4.  **Reporting & Analytics**
    - Monthly service volume reports
    - Digital marketing ROI reports
    - Revenue summaries

5.  **Partner Integrations**
    - Hospital & pharmacy referral tracker
    - Commission and invoice sharing
    - Referral document upload & status sync

6.  **Business & NGO Networking**
    - Track engagement with orgs, banks, schools
    - Priority service levels
    - Revenue sharing from referrals

7.  **International Referrals**
    - Global partner hospital directory
    - Referral request system
    - Visa & travel documentation tracking
    - Cross-border commission reporting
