# Master Database Schema (PostgreSQL)

This document defines the official database schema for the Home-to-Home Care Automation Platform. All new migrations and models must conform to these table structures and relationships.

*Note: The `staff` table reflects the corrected migration we implemented, not the initial schema document.*

## 1. Table Definitions

```
-- 1. patients
CREATE TABLE patients (
    id SERIAL PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    date_of_birth DATE,
    gender VARCHAR(10),
    address TEXT,
    phone_number VARCHAR(255) UNIQUE,
    email VARCHAR(255) UNIQUE,
    emergency_contact TEXT,
    geolocation GEOGRAPHY(POINT, 4326),
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- 2. staff (Reflects the actual migration used)
CREATE TABLE staff (
    id SERIAL PRIMARY KEY,
    first_name VARCHAR(255) NOT NULL,
    last_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    phone VARCHAR(255) NULL,
    position VARCHAR(255) NULL,
    department VARCHAR(255) NULL,
    role VARCHAR(255) NOT NULL DEFAULT 'Caregiver',
    status VARCHAR(50) NOT NULL DEFAULT 'Active',
    hire_date DATE NULL,
    photo VARCHAR(255) NULL,
    user_id BIGINT UNSIGNED NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- 3. caregiver_assignments
CREATE TABLE caregiver_assignments (
    id SERIAL PRIMARY KEY,
    staff_id INTEGER NOT NULL REFERENCES staff(id) ON DELETE CASCADE,
    patient_id INTEGER NOT NULL REFERENCES patients(id) ON DELETE CASCADE,
    shift_start TIMESTAMP,
    shift_end TIMESTAMP,
    status VARCHAR(255) DEFAULT 'Assigned',
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- 4. visit_services
CREATE TABLE visit_services (
    id SERIAL PRIMARY KEY,
    patient_id INTEGER NOT NULL REFERENCES patients(id) ON DELETE CASCADE,
    staff_id INTEGER NOT NULL REFERENCES staff(id) ON DELETE SET NULL,
    scheduled_at TIMESTAMP,
    check_in_time TIMESTAMP,
    check_out_time TIMESTAMP,
    visit_notes TEXT,
    prescription_file TEXT,
    vitals_file TEXT,
    status VARCHAR DEFAULT 'Pending',
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- 5. messages
CREATE TABLE messages (
    id SERIAL PRIMARY KEY,
    sender_id INTEGER REFERENCES staff(id) ON DELETE CASCADE,
    receiver_id INTEGER REFERENCES patients(id) ON DELETE CASCADE,
    message TEXT,
    type VARCHAR(10) DEFAULT 'text',
    read_at TIMESTAMP,
    created_at TIMESTAMP
);

-- 6. invoices
CREATE TABLE invoices (
    id SERIAL PRIMARY KEY,
    patient_id INTEGER NOT NULL REFERENCES patients(id) ON DELETE CASCADE,
    service_id INTEGER NOT NULL REFERENCES visit_services(id) ON DELETE CASCADE,
    amount NUMERIC NOT NULL,
    status VARCHAR(255) DEFAULT 'Pending',
    paid_at TIMESTAMP,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- 7. insurance_claims
CREATE TABLE insurance_claims (
    id SERIAL PRIMARY KEY,
    patient_id INTEGER NOT NULL REFERENCES patients(id) ON DELETE CASCADE,
    insurance_company VARCHAR(255),
    claim_status VARCHAR(255) DEFAULT 'Submitted',
    coverage_amount NUMERIC,
    submitted_at TIMESTAMP,
    processed_at TIMESTAMP,
    created_at TIMESTAMP
);

-- 8. inventory_items
CREATE TABLE inventory_items (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    status VARCHAR(255) DEFAULT 'Available',
    location TEXT,
    expiry_date DATE,
    photo TEXT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- 9. admin_task_tracking
CREATE TABLE admin_task_tracking (
    id SERIAL PRIMARY KEY,
    staff_id INTEGER NOT NULL REFERENCES staff(id) ON DELETE CASCADE,
    visit_service_id INTEGER REFERENCES visit_services(id) ON DELETE SET NULL,
    task_status VARCHAR(255) DEFAULT 'Assigned',
    remarks TEXT,
    assigned_date TIMESTAMP,
    completed_date TIMESTAMP,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- 10. partner_hospitals
CREATE TABLE partner_hospitals (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    address TEXT,
    contact_email VARCHAR(255),
    contact_phone VARCHAR(255),
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- 11. referrals
CREATE TABLE referrals (
    id SERIAL PRIMARY KEY,
    patient_id INTEGER NOT NULL REFERENCES patients(id) ON DELETE CASCADE,
    partner_hospital_id INTEGER NOT NULL REFERENCES partner_hospitals(id) ON DELETE CASCADE,
    reason TEXT,
    documents TEXT,
    status VARCHAR(255) DEFAULT 'Requested',
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- 12. marketing_campaigns
CREATE TABLE marketing_campaigns (
    id SERIAL PRIMARY KEY,
    platform VARCHAR(255),
    campaign_name VARCHAR(255),
    landing_page_url TEXT,
    start_date DATE,
    end_date DATE,
    roi_report_url TEXT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- 13. international_referrals
CREATE TABLE international_referrals (
    id SERIAL PRIMARY KEY,
    patient_id INTEGER NOT NULL REFERENCES patients(id) ON DELETE CASCADE,
    partner_country VARCHAR(255),
    hospital_name VARCHAR(255),
    visa_status VARCHAR(255),
    documents_uploaded TEXT,
    revenue_share_ratio NUMERIC,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- 14. events
CREATE TABLE events (
    id SERIAL PRIMARY KEY,
    title VARCHAR(255),
    description TEXT,
    event_date DATE,
    is_free_service BOOLEAN DEFAULT FALSE,
    broadcast_status VARCHAR(255) DEFAULT 'Draft',
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- 15. ngo_networks
CREATE TABLE ngo_networks (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255),
    org_type VARCHAR(255),
    engagement_level VARCHAR(255),
    referral_count INTEGER DEFAULT 0,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

```

## 2. Entity Relationship Summary

- **staff ↔ patients**:
    - Via `caregiver_assignments` (`staff_id` ↔ `patient_id`)
    - Via `visit_services` (`staff_id` ↔ `patient_id`)
    - Via `messages` (`sender_id` ↔ `receiver_id`)
- **patients ↔ services**:
    - Via `visit_services`
    - Via `invoices`
- **patients ↔ insurance_claims** (One-to-Many)
- **staff ↔ admin_task_tracking** (One-to-Many)
- **visit_services ↔ admin_task_tracking** (Optional One-to-One/Many)
- **partner_hospitals ↔ referrals** (One-to-Many)
- **patients** ↔ **referrals** (One-to-Many)
- **patients ↔ international_referrals** (One-to-Many)
- **marketing_campaigns ↔ patients** (Optional, via a pivot table)
- **events ↔ broadcast** (Optional, via an extension table)