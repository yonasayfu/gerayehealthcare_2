# Project Roadmap & Vision: Home-to-Home Care Platform

This document outlines the vision, architecture, features, and technology stack for the Home-to-Home Care Platform.

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
