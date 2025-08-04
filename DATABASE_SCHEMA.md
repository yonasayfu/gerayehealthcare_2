# Master Database Schema (PostgreSQL)

This document defines the official database schema for the Home-to-Home Care Automation Platform. It is organized by functional modules.

---

## 1. Implemented Modules

### Core & Patient Management
```sql
-- Patients
CREATE TABLE patients (
    id SERIAL PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    date_of_birth DATE,
    gender VARCHAR(10),
    address TEXT,
    phone_number VARCHAR(255) UNIQUE,
    email VARCHAR(255) UNIQUE,
    emergency_contact TEXT,
    geolocation TEXT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- Services
CREATE TABLE services (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price NUMERIC(10, 2) NOT NULL,
    duration INT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### Staff & Administrative Tools
```sql
-- Staff
CREATE TABLE staff (
    id SERIAL PRIMARY KEY,
    first_name VARCHAR(255) NOT NULL,
    last_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    phone VARCHAR(255),
    position VARCHAR(255),
    department VARCHAR(255),
    status VARCHAR(50) DEFAULT 'Active',
    hire_date DATE,
    photo VARCHAR(255),
    user_id BIGINT UNSIGNED,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- Caregiver Assignments
CREATE TABLE caregiver_assignments (
    id SERIAL PRIMARY KEY,
    staff_id BIGINT UNSIGNED NOT NULL,
    patient_id BIGINT UNSIGNED NOT NULL,
    shift_start TIMESTAMP,
    shift_end TIMESTAMP,
    status VARCHAR(255) DEFAULT 'Assigned',
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- Staff Availabilities
CREATE TABLE staff_availabilities (
    id SERIAL PRIMARY KEY,
    staff_id BIGINT UNSIGNED NOT NULL,
    start_time TIMESTAMP NOT NULL,
    end_time TIMESTAMP NOT NULL,
    status VARCHAR(255) NOT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- Leave Requests
CREATE TABLE leave_requests (
    id SERIAL PRIMARY KEY,
    staff_id BIGINT UNSIGNED NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    reason TEXT,
    status VARCHAR(255) DEFAULT 'Pending',
    admin_notes TEXT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- Task Delegations
CREATE TABLE task_delegations (
    id SERIAL PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    assigned_to BIGINT UNSIGNED NOT NULL,
    due_date DATE NOT NULL,
    status VARCHAR(50) DEFAULT 'Pending',
    notes TEXT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### Inventory Management
```sql
-- Suppliers
CREATE TABLE suppliers (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    contact_person VARCHAR(255),
    email VARCHAR(255),
    phone VARCHAR(255),
    address TEXT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- Inventory Items
CREATE TABLE inventory_items (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    item_category VARCHAR(255),
    item_type VARCHAR(255),
    serial_number VARCHAR(255) UNIQUE,
    purchase_date DATE,
    warranty_expiry DATE,
    supplier_id BIGINT UNSIGNED,
    assigned_to_type VARCHAR(255),
    assigned_to_id BIGINT UNSIGNED,
    last_maintenance_date DATE,
    next_maintenance_due DATE,
    maintenance_schedule VARCHAR(255),
    notes TEXT,
    status VARCHAR(255) DEFAULT 'Available',
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- Inventory Requests
CREATE TABLE inventory_requests (
    id SERIAL PRIMARY KEY,
    requester_id BIGINT UNSIGNED NOT NULL,
    approver_id BIGINT UNSIGNED,
    item_id BIGINT UNSIGNED NOT NULL,
    quantity_requested INT DEFAULT 1,
    quantity_approved INT,
    reason TEXT,
    status VARCHAR(255) DEFAULT 'Pending',
    priority VARCHAR(255) DEFAULT 'Normal',
    needed_by_date DATE,
    approved_at TIMESTAMP,
    fulfilled_at TIMESTAMP,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- Inventory Transactions
CREATE TABLE inventory_transactions (
    id SERIAL PRIMARY KEY,
    item_id BIGINT UNSIGNED NOT NULL,
    request_id BIGINT UNSIGNED,
    from_location VARCHAR(255),
    to_location VARCHAR(255),
    from_assigned_to_type VARCHAR(255),
    from_assigned_to_id BIGINT UNSIGNED,
    to_assigned_to_type VARCHAR(255),
    to_assigned_to_id BIGINT UNSIGNED,
    quantity INT NOT NULL,
    transaction_type VARCHAR(255) NOT NULL,
    performed_by_id BIGINT UNSIGNED NOT NULL,
    notes TEXT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- Inventory Maintenance Records
CREATE TABLE inventory_maintenance_records (
    id SERIAL PRIMARY KEY,
    item_id BIGINT UNSIGNED NOT NULL,
    scheduled_date DATE,
    actual_date DATE,
    performed_by VARCHAR(255),
    cost NUMERIC(10, 2),
    description TEXT,
    next_due_date DATE,
    status VARCHAR(255) DEFAULT 'Scheduled',
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- Inventory Alerts
CREATE TABLE inventory_alerts (
    id SERIAL PRIMARY KEY,
    item_id BIGINT UNSIGNED,
    alert_type VARCHAR(255) NOT NULL,
    threshold_value VARCHAR(255),
    message TEXT NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    triggered_at TIMESTAMP,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### Marketing Management
```sql
-- Marketing Campaigns
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

-- Marketing Leads
CREATE TABLE marketing_leads (
    id SERIAL PRIMARY KEY,
    lead_code VARCHAR(255) UNIQUE NOT NULL,
    source_campaign_id BIGINT UNSIGNED,
    first_name VARCHAR(255) NOT NULL,
    last_name VARCHAR(255) NOT NULL,
    email VARCHAR(255),
    phone VARCHAR(255),
    country VARCHAR(255),
    utm_source VARCHAR(255),
    utm_campaign VARCHAR(255),
    utm_medium VARCHAR(255),
    landing_page_id BIGINT UNSIGNED,
    lead_score INT DEFAULT 0,
    status VARCHAR(255) DEFAULT 'New',
    assigned_staff_id BIGINT UNSIGNED,
    converted_patient_id BIGINT UNSIGNED,
    conversion_date TIMESTAMP,
    notes TEXT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### Events Management
```sql
-- Events
CREATE TABLE events (
    id SERIAL PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    event_date DATE NOT NULL,
    is_free_service BOOLEAN DEFAULT FALSE,
    broadcast_status VARCHAR(255) DEFAULT 'Draft',
    is_pagume_campaign BOOLEAN DEFAULT FALSE,
    location VARCHAR(255),
    region VARCHAR(255),
    woreda VARCHAR(255),
    created_by_staff_id BIGINT UNSIGNED,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

---

## 2. In Progress & Future Modules

### Visit & Service Management
```sql
-- Visit Services
CREATE TABLE visit_services (
    id SERIAL PRIMARY KEY,
    patient_id BIGINT UNSIGNED NOT NULL,
    staff_id BIGINT UNSIGNED,
    scheduled_at TIMESTAMP,
    check_in_time TIMESTAMP,
    check_out_time TIMESTAMP,
    visit_notes TEXT,
    prescription_file VARCHAR(255),
    vitals_file VARCHAR(255),
    status VARCHAR(255) DEFAULT 'Pending',
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### Billing & Payments
```sql
-- Invoices
CREATE TABLE invoices (
    id SERIAL PRIMARY KEY,
    patient_id BIGINT UNSIGNED NOT NULL,
    service_id BIGINT UNSIGNED NOT NULL,
    invoice_number VARCHAR(255) UNIQUE NOT NULL,
    invoice_date DATE NOT NULL,
    due_date DATE NOT NULL,
    subtotal NUMERIC(10, 2) NOT NULL,
    tax_amount NUMERIC(10, 2) DEFAULT 0.00,
    grand_total NUMERIC(10, 2) NOT NULL,
    amount NUMERIC(10, 2) NOT NULL,
    status VARCHAR(255) DEFAULT 'Pending',
    paid_at TIMESTAMP,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- Staff Payouts
CREATE TABLE staff_payouts (
    id SERIAL PRIMARY KEY,
    staff_id BIGINT UNSIGNED NOT NULL,
    total_amount NUMERIC(10, 2) NOT NULL,
    payout_date DATE NOT NULL,
    status VARCHAR(255) DEFAULT 'Completed',
    notes TEXT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```
