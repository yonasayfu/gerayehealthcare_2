# Project Roadmap & Vision: Home-to-Home Care Platform

This document outlines the vision, architecture, features, and technology stack for the Home-to-Home Care Platform.


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

## 2. Core Feature Modules (Checklist)

1. [✓] Patient Management
   - Patient registration and medical profile (CRUD, search, export, print)
   - Medical history tracking (via visit services)
   - Appointment scheduling (via visit services)
   - Emergency contact info
   - Geolocation/address tracking for home visits
   - Patient code generation
   - Fayda ID integration
   - Marketing acquisition tracking (source, campaign, lead)

2. [✓] Caregiver/Nurse Management
   - Staff registration and management (CRUD, search, export, print)
   - Role assignment (via Spatie permissions)
   - Hourly rate tracking
   - Caregiver assignments to patients (CRUD, search, export, print)
   - Shift scheduling and availability (CRUD, search, calendar view)
   - Leave requests (CRUD, search, export, print)
   - Staff payouts (CRUD, search, export, print)

3. [✓] Admin & HR Tools
   - Dashboard for organization overview
   - User management (CRUD, roles/permissions, search, export, print)
   - Service management (CRUD, search, export, print)
   - Task delegation (CRUD, search, export, print)
   - Invoices (CRUD, search, export, print)
   - Messages (CRUD)
   - Notifications (CRUD)

4. [✓] Digital Marketing Tracker
   - Lead sources (CRUD, search, export, print)
   - Marketing platforms (CRUD, search, export, print)
   - Marketing campaigns (CRUD, search, export, print, enhanced with budget, goals, UTMs)
   - Campaign metrics (CRUD, search, export, print)
   - Landing pages (CRUD, search, export, print)
   - Marketing leads (CRUD, search, export, print, with conversion to patient)
   - Campaign content (CRUD, search, export, print)
   - Marketing budgets (CRUD, search, export, print)
   - Marketing tasks (CRUD, search, export, print)
   - Marketing analytics dashboard (data retrieval)

5. [✓] Inventory Management
   - Suppliers (CRUD, search, export, print)
   - Inventory items (CRUD, search, export, print, with quantity, reorder level)
   - Inventory requests (CRUD, search, export, print)
   - Inventory transactions (CRUD, search, export, print)
   - Inventory maintenance records (CRUD, search, export, print)
   - Inventory alerts (CRUD, search, export, print)

6. [✓] Events Management
   - Events (CRUD, search, export, print)
   - Event recommendations (CRUD, search, export, print, with patient linking)
   - Eligibility criteria (CRUD, search, export, print)
   - Event staff assignments (CRUD, search, export, print)
   - Event broadcasts (CRUD, search, export, print)
   - Event participants (CRUD, search, export, print)

7. [✓] Insurance Coordination
   - Insurance companies (CRUD, search, export, print)
   - Corporate clients (CRUD, search, export, print)
   - Insurance policies (CRUD, search, export, print)
   - Employee insurance records (CRUD, search, export, print)
   - Insurance claims (CRUD, search, export, print, with email tracking)
   - Exchange rates (CRUD, search, export, print)
   - Ethiopian calendar days (CRUD, search, export, print)

8. [✓] Visit & Service Management (In Progress)
   - Daily visit schedule for caregivers
   - Check-in/check-out via GPS (proof of visit)
   - Digital notes and reports from each visit
   - Upload prescriptions, vitals, and images
   - Cost tracking, invoicing, service and assignment linking

9. [✓] Communication
   - Secure in-app chat between staff and patients
   - Notification system (reminders, updates)
   - Video consultation option



11. [✓] Medical Records & Documentation
    - Digital prescriptions
    - Lab result uploads
    - Progress notes and treatment plans

12. [X] Reporting & Analytics
    - Monthly service volume reports
    - Digital marketing ROI reports
    - Revenue summaries

13. [✓] Partner Integrations
    - Hospital & pharmacy referral tracker
    - Commission and invoice sharing
    - Referral document upload & status sync

14. [✓] Business & NGO Networking
    - Track engagement with orgs, banks, schools
    - Priority service levels
    - Revenue sharing from referrals




## 3. Technology Stack

- **Backend**: Laravel (PHP)
- **Frontend**: Vue.js (TypeScript) with Inertia.js
- **Build Tool**: Vite
- **Database**: PostgreSQL
- **Testing Framework**: Pest (PHPUnit for Laravel)
- **Deployment**: Docker, Render.com

## 4. Development Progress Summary

### Day 1-n Accomplishments:

- Initial project setup with Laravel, Vue, Inertia, Vite.
- Implemented core authentication and authorization (Spatie/Laravel-Permission).
- Developed a DRY validation architecture.
- Implemented comprehensive CRUD, search, export, and print functionalities for:
    - Patient Management
    - Staff & Administrative Tools (including Staff, Caregiver Assignments, Staff Availabilities, Leave Requests, Task Delegations, Staff Payouts, Messages, Notifications)
    - Digital Marketing Tracker (including Lead Sources, Marketing Platforms, Marketing Campaigns, Campaign Metrics, Landing Pages, Marketing Leads, Campaign Content, Marketing Budgets, Marketing Tasks, Marketing Analytics Dashboard)
    - Inventory Management (including Suppliers, Inventory Items, Inventory Requests, Inventory Transactions, Inventory Maintenance Records, Inventory Alerts)
    - Events Management (including Events, Event Recommendations, Eligibility Criteria, Event Staff Assignments, Event Broadcasts, Event Participants)
    - Insurance Coordination (including Insurance Companies, Corporate Clients, Insurance Policies, Employee Insurance Records, Insurance Claims, Exchange Rates, Ethiopian Calendar Days)
    - Visit & Service Management (partially, with core CRUD, GPS check-in/out, cost, invoicing, service/assignment linking, Invoice Items, Invoices, Payout Visit Service)
- Integrated dynamic sidebar navigation based on user roles.
- Established a standard module implementation workflow.
- Implemented backend export/print methods for many controllers.

#### Recent Global UX/Polish (Completed)

- Standardized UI elements (buttons, etc.) across modules (Staff)
- Fixed Spatie\LaravelData\Data not found in `CreateStaffDTO`
- Rechecked index number and CSV export for consistency
- Made print current/single UI more visually appealing ("fancy")
- Moved search icon to right inside search box

### Plan for Day n:

- All backend issues from `ISSUE_TRACKER.md` have been addressed.
- Remaining tasks are primarily frontend-related or require manual intervention/debugging.

