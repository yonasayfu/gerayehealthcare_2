# Master Database Schema (PostgreSQL)

This document defines the official  

## Core Laravel & Spatie Tables

```sql
-- users
CREATE TABLE users (
    id BIGSERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

-- password_reset_tokens
CREATE TABLE password_reset_tokens (
    email VARCHAR(255) PRIMARY KEY,
    token VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL
);

-- sessions
CREATE TABLE sessions (
    id VARCHAR(255) PRIMARY KEY,
    user_id BIGINT NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    payload TEXT NOT NULL,
    last_activity INTEGER NOT NULL
);
CREATE INDEX sessions_user_id_index ON sessions (user_id);
CREATE INDEX sessions_last_activity_index ON sessions (last_activity);

-- cache
CREATE TABLE cache (
    key VARCHAR(255) PRIMARY KEY,
    value TEXT NOT NULL,
    expiration INTEGER NOT NULL
);

-- cache_locks
CREATE TABLE cache_locks (
    key VARCHAR(255) PRIMARY KEY,
    owner VARCHAR(255) NOT NULL,
    expiration INTEGER NOT NULL
);

-- jobs
CREATE TABLE jobs (
    id BIGSERIAL PRIMARY KEY,
    queue VARCHAR(255) NOT NULL,
    payload TEXT NOT NULL,
    attempts SMALLINT NOT NULL,
    reserved_at INTEGER NULL,
    available_at INTEGER NOT NULL,
    created_at INTEGER NOT NULL
);
CREATE INDEX jobs_queue_index ON jobs (queue);

-- job_batches
CREATE TABLE job_batches (
    id VARCHAR(255) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    total_jobs INTEGER NOT NULL,
    pending_jobs INTEGER NOT NULL,
    failed_jobs INTEGER NOT NULL,
    failed_job_ids TEXT NOT NULL,
    options TEXT NULL,
    cancelled_at INTEGER NULL,
    created_at INTEGER NOT NULL,
    finished_at INTEGER NULL
);

-- failed_jobs
CREATE TABLE failed_jobs (
    id BIGSERIAL PRIMARY KEY,
    uuid VARCHAR(255) UNIQUE NOT NULL,
    connection TEXT NOT NULL,
    queue TEXT NOT NULL,
    payload TEXT NOT NULL,
    exception TEXT NOT NULL,
    failed_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- personal_access_tokens
CREATE TABLE personal_access_tokens (
    id BIGSERIAL PRIMARY KEY,
    tokenable_type VARCHAR(255) NOT NULL,
    tokenable_id BIGINT NOT NULL,
    name TEXT NOT NULL,
    token VARCHAR(64) UNIQUE NOT NULL,
    abilities TEXT NULL,
    last_used_at TIMESTAMP NULL,
    expires_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
CREATE INDEX personal_access_tokens_tokenable_type_tokenable_id_index ON personal_access_tokens (tokenable_type, tokenable_id);
CREATE INDEX personal_access_tokens_expires_at_index ON personal_access_tokens (expires_at);

-- permissions (from spatie/laravel-permission)
CREATE TABLE permissions (
    id BIGSERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    guard_name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    UNIQUE (name, guard_name)
);

-- roles (from spatie/laravel-permission)
CREATE TABLE roles (
    id BIGSERIAL PRIMARY KEY,
    team_id BIGINT NULL, -- if teams are enabled
    name VARCHAR(255) NOT NULL,
    guard_name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    UNIQUE (name, guard_name)
);
-- If teams are enabled, add unique constraint: UNIQUE (team_id, name, guard_name)

-- model_has_permissions (from spatie/laravel-permission)
CREATE TABLE model_has_permissions (
    permission_id BIGINT NOT NULL,
    model_type VARCHAR(255) NOT NULL,
    model_id BIGINT NOT NULL,
    team_id BIGINT NULL, -- if teams are enabled
    PRIMARY KEY (permission_id, model_id, model_type), -- if teams are not enabled
    -- If teams are enabled: PRIMARY KEY (team_id, permission_id, model_id, model_type)
    FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE
);
CREATE INDEX model_has_permissions_model_id_model_type_index ON model_has_permissions (model_id, model_type);
-- If teams are enabled: CREATE INDEX model_has_permissions_team_foreign_key_index ON model_has_permissions (team_id);

-- model_has_roles (from spatie/laravel-permission)
CREATE TABLE model_has_roles (
    role_id BIGINT NOT NULL,
    model_type VARCHAR(255) NOT NULL,
    model_id BIGINT NOT NULL,
    team_id BIGINT NULL, -- if teams are enabled
    PRIMARY KEY (role_id, model_id, model_type), -- if teams are not enabled
    -- If teams are enabled: PRIMARY KEY (team_id, role_id, model_id, model_type)
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE
);
CREATE INDEX model_has_roles_model_id_model_type_index ON model_has_roles (model_id, model_type);
-- If teams are enabled: CREATE INDEX model_has_roles_team_foreign_key_index ON model_has_roles (team_id);

-- role_has_permissions (from spatie/laravel-permission)
CREATE TABLE role_has_permissions (
    permission_id BIGINT NOT NULL,
    role_id BIGINT NOT NULL,
    PRIMARY KEY (permission_id, role_id),
    FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE
);
```

## Application Modules

### 1. Patient Management

```sql
-- patients
CREATE TABLE patients (
    id BIGSERIAL PRIMARY KEY,
    patient_code VARCHAR(255) UNIQUE NOT NULL,
    fayda_id VARCHAR(255) UNIQUE NULL,
    full_name VARCHAR(255) NOT NULL,
    date_of_birth DATE NULL,
    ethiopian_date_of_birth VARCHAR(255) NULL,
    gender VARCHAR(10) NULL,
    address TEXT NULL,
    phone_number VARCHAR(255) UNIQUE NULL,
    email VARCHAR(255) UNIQUE NULL,
    emergency_contact TEXT NULL,
    source VARCHAR(255) NULL,
    geolocation TEXT NULL,
    registered_by_staff_id BIGINT NULL,
    registered_by_caregiver_id BIGINT NULL,
    acquisition_source_id BIGINT NULL,
    marketing_campaign_id BIGINT NULL,
    utm_campaign VARCHAR(255) NULL,
    utm_source VARCHAR(255) NULL,
    utm_medium VARCHAR(255) NULL,
    lead_id BIGINT NULL,
    acquisition_cost NUMERIC(10, 2) NULL,
    acquisition_date TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (registered_by_staff_id) REFERENCES staff(id) ON DELETE SET NULL,
    FOREIGN KEY (registered_by_caregiver_id) REFERENCES caregiver_assignments(id) ON DELETE SET NULL,
    FOREIGN KEY (acquisition_source_id) REFERENCES lead_sources(id) ON DELETE SET NULL,
    FOREIGN KEY (marketing_campaign_id) REFERENCES marketing_campaigns(id) ON DELETE SET NULL,
    FOREIGN KEY (lead_id) REFERENCES marketing_leads(id) ON DELETE SET NULL
);
```

### 2. Staff & Administrative Tools

```sql
-- staff
CREATE TABLE staff (
    id BIGSERIAL PRIMARY KEY,
    first_name VARCHAR(255) NOT NULL,
    last_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    phone VARCHAR(255) NULL,
    position VARCHAR(255) NULL,
    department VARCHAR(255) NULL,
    role VARCHAR(255) DEFAULT 'Caregiver',
    status VARCHAR(50) DEFAULT 'Active',
    hourly_rate NUMERIC(8, 2) NULL,
    hire_date DATE NULL,
    photo VARCHAR(255) NULL,
    user_id BIGINT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- caregiver_assignments
CREATE TABLE caregiver_assignments (
    id BIGSERIAL PRIMARY KEY,
    staff_id BIGINT NOT NULL,
    patient_id BIGINT NOT NULL,
    shift_start TIMESTAMP NULL,
    shift_end TIMESTAMP NULL,
    status VARCHAR(255) DEFAULT 'Assigned',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (staff_id) REFERENCES staff(id) ON DELETE CASCADE,
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE
);

-- staff_availabilities
CREATE TABLE staff_availabilities (
    id BIGSERIAL PRIMARY KEY,
    staff_id BIGINT NOT NULL,
    start_time TIMESTAMP NOT NULL,
    end_time TIMESTAMP NOT NULL,
    status VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (staff_id) REFERENCES staff(id) ON DELETE CASCADE
);
CREATE INDEX idx_staff_availabilities_staff_id ON staff_availabilities (staff_id);
CREATE INDEX idx_staff_availabilities_staff_start ON staff_availabilities (staff_id, start_time);
CREATE INDEX idx_staff_availabilities_start_end ON staff_availabilities (start_time, end_time);

-- leave_requests
CREATE TABLE leave_requests (
    id BIGSERIAL PRIMARY KEY,
    staff_id BIGINT NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    reason TEXT NULL,
    status VARCHAR(255) DEFAULT 'Pending',
    admin_notes TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (staff_id) REFERENCES staff(id) ON DELETE CASCADE
);

-- task_delegations
CREATE TABLE task_delegations (
    id BIGSERIAL PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    assigned_to BIGINT NOT NULL,
    due_date DATE NOT NULL,
    status VARCHAR(50) DEFAULT 'Pending',
    notes TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (assigned_to) REFERENCES staff(id) ON DELETE CASCADE
);

-- staff_payouts
CREATE TABLE staff_payouts (
    id BIGSERIAL PRIMARY KEY,
    staff_id BIGINT NOT NULL,
    total_amount NUMERIC(10, 2) NOT NULL,
    payout_date DATE NOT NULL,
    status VARCHAR(255) DEFAULT 'Completed',
    notes TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (staff_id) REFERENCES staff(id) ON DELETE CASCADE
);
CREATE INDEX idx_staff_payouts_staff_id ON staff_payouts (staff_id);
CREATE INDEX idx_staff_payouts_payout_date ON staff_payouts (payout_date);
CREATE INDEX idx_staff_payouts_staff_payout_date ON staff_payouts (staff_id, payout_date);
CREATE INDEX idx_staff_payouts_status ON staff_payouts (status);

-- messages
CREATE TABLE messages (
    id BIGSERIAL PRIMARY KEY,
    sender_id BIGINT NULL,
    receiver_id BIGINT NULL,
    message TEXT NULL,
    attachment_path VARCHAR(255) NULL,
    attachment_filename VARCHAR(255) NULL,
    attachment_mime_type VARCHAR(255) NULL,
    read_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE
);

-- notifications
CREATE TABLE notifications (
    id UUID PRIMARY KEY,
    type VARCHAR(255) NOT NULL,
    notifiable_type VARCHAR(255) NOT NULL,
    notifiable_id BIGINT NOT NULL,
    data TEXT NOT NULL,
    read_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
CREATE INDEX notifications_notifiable_type_notifiable_id_index ON notifications (notifiable_type, notifiable_id);
```

### 3. Inventory Management

```sql
-- suppliers
CREATE TABLE suppliers (
    id BIGSERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    contact_person VARCHAR(255) NULL,
    email VARCHAR(255) NULL,
    phone VARCHAR(255) NULL,
    address TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

-- inventory_items
CREATE TABLE inventory_items (
    id BIGSERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT NULL,
    item_category VARCHAR(255) NULL,
    item_type VARCHAR(255) NULL,
    serial_number VARCHAR(255) UNIQUE NULL,
    purchase_date DATE NULL,
    warranty_expiry DATE NULL,
    supplier_id BIGINT NULL,
    assigned_to_type VARCHAR(255) NULL,
    assigned_to_id BIGINT NULL,
    last_maintenance_date DATE NULL,
    next_maintenance_due DATE NULL,
    maintenance_schedule VARCHAR(255) NULL,
    notes TEXT NULL,
    status VARCHAR(255) DEFAULT 'Available',
    quantity_on_hand INTEGER DEFAULT 0,
    reorder_level INTEGER DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id)
);

-- inventory_requests
CREATE TABLE inventory_requests (
    id BIGSERIAL PRIMARY KEY,
    requester_id BIGINT NOT NULL,
    approver_id BIGINT NULL,
    item_id BIGINT NOT NULL,
    quantity_requested INTEGER DEFAULT 1,
    quantity_approved INTEGER NULL,
    reason TEXT NULL,
    status VARCHAR(255) DEFAULT 'Pending',
    priority VARCHAR(255) DEFAULT 'Normal',
    needed_by_date DATE NULL,
    approved_at TIMESTAMP NULL,
    fulfilled_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (requester_id) REFERENCES staff(id),
    FOREIGN KEY (approver_id) REFERENCES staff(id),
    FOREIGN KEY (item_id) REFERENCES inventory_items(id)
);

-- inventory_transactions
CREATE TABLE inventory_transactions (
    id BIGSERIAL PRIMARY KEY,
    item_id BIGINT NOT NULL,
    request_id BIGINT NULL,
    from_location VARCHAR(255) NULL,
    to_location VARCHAR(255) NULL,
    from_assigned_to_type VARCHAR(255) NULL,
    from_assigned_to_id BIGINT NULL,
    to_assigned_to_type VARCHAR(255) NULL,
    to_assigned_to_id BIGINT NULL,
    quantity INTEGER NOT NULL,
    transaction_type VARCHAR(255) NOT NULL,
    performed_by_id BIGINT NOT NULL,
    notes TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (item_id) REFERENCES inventory_items(id),
    FOREIGN KEY (request_id) REFERENCES inventory_requests(id),
    FOREIGN KEY (performed_by_id) REFERENCES staff(id)
);

-- inventory_maintenance_records
CREATE TABLE inventory_maintenance_records (
    id BIGSERIAL PRIMARY KEY,
    item_id BIGINT NOT NULL,
    scheduled_date DATE NULL,
    actual_date DATE NULL,
    performed_by VARCHAR(255) NULL,
    cost NUMERIC(10, 2) NULL,
    description TEXT NULL,
    next_due_date DATE NULL,
    status VARCHAR(255) DEFAULT 'Scheduled',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (item_id) REFERENCES inventory_items(id)
);

-- inventory_alerts
CREATE TABLE inventory_alerts (
    id BIGSERIAL PRIMARY KEY,
    item_id BIGINT NULL,
    alert_type VARCHAR(255) NOT NULL,
    threshold_value VARCHAR(255) NULL,
    message TEXT NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    triggered_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (item_id) REFERENCES inventory_items(id)
);
```
-- Main entity for all external organizations
CREATE TABLE partners (
    id BIGSERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    -- (From new proposal) 'type' is a clear name for categorization
    type VARCHAR(100) NOT NULL, -- 'Corporate', 'NGO', 'School', 'Bank', 'Government Agency'
    contact_person VARCHAR(255) NULL,
    email VARCHAR(255) UNIQUE NULL,
    phone VARCHAR(255) NULL,
    address TEXT NULL,
    -- (From new proposal) 'engagement_status' is a great, descriptive name for the partner's lifecycle
    engagement_status VARCHAR(50) DEFAULT 'Prospect', -- 'Prospect', 'Active', 'Inactive'
    -- (From my proposal) The staff member managing the relationship
    account_manager_id BIGINT NULL,
    notes TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (account_manager_id) REFERENCES staff(id) ON DELETE SET NULL
);

-- This table is CRITICAL for flexibility, separating the "who" from the "what"
CREATE TABLE partner_agreements (
    id BIGSERIAL PRIMARY KEY,
    partner_id BIGINT NOT NULL,
    agreement_title VARCHAR(255) NOT NULL,
    agreement_type VARCHAR(100) NOT NULL, -- 'Referral Commission', 'Priority Service', 'Co-Marketing'
    status VARCHAR(50) DEFAULT 'Draft', -- 'Draft', 'Active', 'Expired', 'Terminated'
    start_date DATE NOT NULL,
    end_date DATE NULL,
    -- (From new proposal) 'priority_level' belongs here, as it's a term of an agreement
    priority_service_level VARCHAR(50) NULL, -- 'Standard', 'Preferred', 'Premium'
    -- (From my proposal) Detailed commission terms belong in the agreement
    commission_type VARCHAR(50) NULL, -- 'Percentage', 'FixedAmountPerPatient'
    commission_rate NUMERIC(8, 2) NULL,
    terms_document_path VARCHAR(255) NULL,
    signed_by_staff_id BIGINT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (partner_id) REFERENCES partners(id) ON DELETE CASCADE,
    FOREIGN KEY (signed_by_staff_id) REFERENCES staff(id) ON DELETE SET NULL
);

-- Tracks the referral event itself, linking all relevant entities
CREATE TABLE referrals (
    id BIGSERIAL PRIMARY KEY,
    partner_id BIGINT NOT NULL,
    agreement_id BIGINT NULL, -- The specific contract governing this referral
    referred_patient_id BIGINT UNIQUE NOT NULL,
    referral_date DATE NOT NULL,
    -- (From new proposal) 'status' for tracking the referral lifecycle is essential
    status VARCHAR(50) DEFAULT 'Pending', -- 'Pending', 'Converted', 'Rejected'
    notes TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (partner_id) REFERENCES partners(id) ON DELETE CASCADE,
    FOREIGN KEY (agreement_id) REFERENCES partner_agreements(id) ON DELETE SET NULL,
    FOREIGN KEY (referred_patient_id) REFERENCES patients(id) ON DELETE CASCADE
);

-- The financial transaction log for commissions. This provides a clear audit trail.
CREATE TABLE partner_commissions (
    id BIGSERIAL PRIMARY KEY,
    agreement_id BIGINT NOT NULL,
    referral_id BIGINT NOT NULL,
    invoice_id BIGINT NOT NULL, -- The specific invoice that triggered this commission
    commission_amount NUMERIC(10, 2) NOT NULL, -- The calculated amount
    calculation_date DATE NOT NULL,
    payout_date DATE NULL,
    status VARCHAR(50) DEFAULT 'Due', -- 'Due', 'Paid', 'Voided'
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (agreement_id) REFERENCES partner_agreements(id) ON DELETE CASCADE,
    FOREIGN KEY (referral_id) REFERENCES referrals(id) ON DELETE CASCADE,
    FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE CASCADE
);

-- The CRM component for tracking interactions
CREATE TABLE partner_engagements (
    id BIGSERIAL PRIMARY KEY,
    partner_id BIGINT NOT NULL,
    staff_id BIGINT NOT NULL,
    engagement_type VARCHAR(100) NOT NULL, -- 'Meeting', 'Call', 'Email', 'Event'
    summary TEXT NOT NULL,
    engagement_date TIMESTAMP NOT NULL,
    follow_up_date DATE NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (partner_id) REFERENCES partners(id) ON DELETE CASCADE,
    FOREIGN KEY (staff_id) REFERENCES staff(id) ON DELETE CASCADE
);
### 4. Marketing Management

```sql
-- lead_sources
CREATE TABLE lead_sources (
    id BIGSERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    category VARCHAR(255) NULL,
    description TEXT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

-- marketing_platforms
CREATE TABLE marketing_platforms (
    id BIGSERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    api_endpoint VARCHAR(255) NULL,
    api_credentials TEXT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

-- marketing_campaigns
CREATE TABLE marketing_campaigns (
    id BIGSERIAL PRIMARY KEY,
    campaign_code VARCHAR(255) UNIQUE NOT NULL,
    platform_id BIGINT NOT NULL,
    campaign_name VARCHAR(255) NULL,
    campaign_type VARCHAR(255) NULL,
    target_audience JSON NULL,
    budget_allocated NUMERIC(15, 2) DEFAULT 0,
    budget_spent NUMERIC(15, 2) DEFAULT 0,
    start_date DATE NULL,
    end_date DATE NULL,
    status VARCHAR(255) DEFAULT 'Draft',
    utm_campaign VARCHAR(255) NULL,
    utm_source VARCHAR(255) NULL,
    utm_medium VARCHAR(255) NULL,
    assigned_staff_id BIGINT NULL,
    created_by_staff_id BIGINT NULL,
    goals JSON NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (platform_id) REFERENCES marketing_platforms(id) ON DELETE CASCADE,
    FOREIGN KEY (assigned_staff_id) REFERENCES staff(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by_staff_id) REFERENCES staff(id) ON DELETE SET NULL
);

-- campaign_metrics
CREATE TABLE campaign_metrics (
    id BIGSERIAL PRIMARY KEY,
    campaign_id BIGINT NOT NULL,
    date DATE NOT NULL,
    impressions INTEGER DEFAULT 0,
    clicks INTEGER DEFAULT 0,
    conversions INTEGER DEFAULT 0,
    cost_per_click NUMERIC(10, 4) NULL,
    cost_per_conversion NUMERIC(10, 2) NULL,
    roi_percentage NUMERIC(8, 2) NULL,
    leads_generated INTEGER DEFAULT 0,
    patients_acquired INTEGER DEFAULT 0,
    revenue_generated NUMERIC(15, 2) DEFAULT 0,
    engagement_rate NUMERIC(5, 2) NULL,
    reach INTEGER NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (campaign_id) REFERENCES marketing_campaigns(id) ON DELETE CASCADE
);
CREATE INDEX campaign_metrics_campaign_id_date_index ON campaign_metrics (campaign_id, date);

-- landing_pages
CREATE TABLE landing_pages (
    id BIGSERIAL PRIMARY KEY,
    page_code VARCHAR(255) UNIQUE NOT NULL,
    campaign_id BIGINT NULL,
    page_title VARCHAR(255) NOT NULL,
    page_url VARCHAR(255) UNIQUE NOT NULL,
    template_used VARCHAR(255) NULL,
    language VARCHAR(255) DEFAULT 'en',
    form_fields JSON NULL,
    conversion_goal VARCHAR(255) NULL,
    views INTEGER DEFAULT 0,
    submissions INTEGER DEFAULT 0,
    conversion_rate NUMERIC(5, 2) NULL,
    is_active BOOLEAN DEFAULT TRUE,
    notes TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (campaign_id) REFERENCES marketing_campaigns(id) ON DELETE SET NULL
);

-- marketing_leads
CREATE TABLE marketing_leads (
    id BIGSERIAL PRIMARY KEY,
    lead_code VARCHAR(255) UNIQUE NOT NULL,
    source_campaign_id BIGINT NULL,
    first_name VARCHAR(255) NOT NULL,
    last_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NULL,
    phone VARCHAR(255) NULL,
    country VARCHAR(255) NULL,
    utm_source VARCHAR(255) NULL,
    utm_campaign VARCHAR(255) NULL,
    utm_medium VARCHAR(255) NULL,
    landing_page_id BIGINT NULL,
    lead_score INTEGER DEFAULT 0,
    status VARCHAR(255) DEFAULT 'New',
    assigned_staff_id BIGINT NULL,
    converted_patient_id BIGINT NULL,
    conversion_date TIMESTAMP NULL,
    notes TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (source_campaign_id) REFERENCES marketing_campaigns(id) ON DELETE SET NULL,
    FOREIGN KEY (landing_page_id) REFERENCES landing_pages(id) ON DELETE SET NULL,
    FOREIGN KEY (assigned_staff_id) REFERENCES staff(id) ON DELETE SET NULL,
    FOREIGN KEY (converted_patient_id) REFERENCES patients(id) ON DELETE SET NULL
);
CREATE INDEX marketing_leads_source_campaign_id_index ON marketing_leads (source_campaign_id);
CREATE INDEX marketing_leads_status_index ON marketing_leads (status);
CREATE INDEX marketing_leads_converted_patient_id_index ON marketing_leads (converted_patient_id);

-- campaign_contents
CREATE TABLE campaign_contents (
    id BIGSERIAL PRIMARY KEY,
    campaign_id BIGINT NULL,
    platform_id BIGINT NULL,
    content_type VARCHAR(255) NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NULL,
    media_url VARCHAR(255) NULL,
    scheduled_post_date TIMESTAMP NULL,
    actual_post_date TIMESTAMP NULL,
    status VARCHAR(255) DEFAULT 'Draft',
    engagement_metrics JSON NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (campaign_id) REFERENCES marketing_campaigns(id) ON DELETE CASCADE,
    FOREIGN KEY (platform_id) REFERENCES marketing_platforms(id) ON DELETE SET NULL
);

-- marketing_budgets
CREATE TABLE marketing_budgets (
    id BIGSERIAL PRIMARY KEY,
    campaign_id BIGINT NULL,
    platform_id BIGINT NULL,
    budget_name VARCHAR(255) NOT NULL,
    description TEXT NULL,
    allocated_amount NUMERIC(15, 2) NOT NULL,
    spent_amount NUMERIC(15, 2) DEFAULT 0,
    period_start DATE NOT NULL,
    period_end DATE NOT NULL,
    status VARCHAR(255) DEFAULT 'Planned',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (campaign_id) REFERENCES marketing_campaigns(id) ON DELETE CASCADE,
    FOREIGN KEY (platform_id) REFERENCES marketing_platforms(id) ON DELETE SET NULL
);

-- marketing_tasks
CREATE TABLE marketing_tasks (
    id BIGSERIAL PRIMARY KEY,
    task_code VARCHAR(255) UNIQUE NOT NULL,
    campaign_id BIGINT NULL,
    assigned_to_staff_id BIGINT NULL,
    related_content_id BIGINT NULL,
    doctor_id BIGINT NULL,
    task_type VARCHAR(255) NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NULL,
    scheduled_at TIMESTAMP NOT NULL,
    completed_at TIMESTAMP NULL,
    status VARCHAR(255) DEFAULT 'Pending',
    notes TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (campaign_id) REFERENCES marketing_campaigns(id) ON DELETE SET NULL,
    FOREIGN KEY (assigned_to_staff_id) REFERENCES staff(id) ON DELETE CASCADE,
    FOREIGN KEY (related_content_id) REFERENCES campaign_contents(id) ON DELETE SET NULL,
    FOREIGN KEY (doctor_id) REFERENCES staff(id) ON DELETE CASCADE
);
```

### 5. Events Management

```sql
-- events
CREATE TABLE events (
    id BIGSERIAL PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NULL,
    event_date DATE NOT NULL,
    is_free_service BOOLEAN DEFAULT FALSE,
    broadcast_status VARCHAR(255) DEFAULT 'Draft',
    is_pagume_campaign BOOLEAN DEFAULT FALSE,
    location VARCHAR(255) NULL,
    region VARCHAR(255) NULL,
    woreda VARCHAR(255) NULL,
    created_by_staff_id BIGINT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (created_by_staff_id) REFERENCES staff(id) ON DELETE SET NULL
);

-- event_recommendations
CREATE TABLE event_recommendations (
    id BIGSERIAL PRIMARY KEY,
    event_id BIGINT NOT NULL,
    source_channel VARCHAR(255) NOT NULL,
    recommended_by_name VARCHAR(255) NULL,
    recommended_by_phone VARCHAR(255) NULL,
    patient_name VARCHAR(255) NOT NULL,
    age INTEGER NULL,
    gender VARCHAR(255) NULL,
    phone_number VARCHAR(255) NULL,
    region VARCHAR(255) NULL,
    woreda VARCHAR(255) NULL,
    reason TEXT NULL,
    linked_patient_id BIGINT NULL,
    notes TEXT NULL,
    status VARCHAR(255) DEFAULT 'pending',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    FOREIGN KEY (linked_patient_id) REFERENCES patients(id) ON DELETE SET NULL
);

-- eligibility_criteria
CREATE TABLE eligibility_criteria (
    id BIGSERIAL PRIMARY KEY,
    event_id BIGINT NOT NULL,
    criteria_title VARCHAR(255) NOT NULL,
    description TEXT NULL,
    operator VARCHAR(255) NOT NULL,
    value VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
);

-- event_staff_assignments
CREATE TABLE event_staff_assignments (
    id BIGSERIAL PRIMARY KEY,
    event_id BIGINT NOT NULL,
    staff_id BIGINT NOT NULL,
    role VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    FOREIGN KEY (staff_id) REFERENCES staff(id) ON DELETE CASCADE
);

-- event_broadcasts
CREATE TABLE event_broadcasts (
    id BIGSERIAL PRIMARY KEY,
    event_id BIGINT NOT NULL,
    channel VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    sent_by_staff_id BIGINT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    FOREIGN KEY (sent_by_staff_id) REFERENCES staff(id) ON DELETE SET NULL
);

-- event_participants
CREATE TABLE event_participants (
    id BIGSERIAL PRIMARY KEY,
    event_id BIGINT NOT NULL,
    patient_id BIGINT NOT NULL,
    status VARCHAR(255) DEFAULT 'registered',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE
);
```

### 6. Insurance Coordination

```sql
-- insurance_companies
CREATE TABLE insurance_companies (
    id BIGSERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    name_amharic VARCHAR(255) NULL,
    contact_person VARCHAR(255) NULL,
    contact_email VARCHAR(255) NULL,
    contact_phone VARCHAR(255) NULL,
    address TEXT NULL,
    address_amharic TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

-- corporate_clients
CREATE TABLE corporate_clients (
    id BIGSERIAL PRIMARY KEY,
    organization_name VARCHAR(255) NOT NULL,
    organization_name_amharic VARCHAR(255) NULL,
    contact_person VARCHAR(255) NULL,
    contact_email VARCHAR(255) NULL,
    contact_phone VARCHAR(255) NULL,
    tin_number VARCHAR(255) NULL,
    trade_license_number VARCHAR(255) NULL,
    address TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

-- insurance_policies
CREATE TABLE insurance_policies (
    id BIGSERIAL PRIMARY KEY,
    insurance_company_id BIGINT NULL,
    corporate_client_id BIGINT NULL,
    service_type VARCHAR(255) NULL,
    service_type_amharic VARCHAR(255) NULL,
    coverage_percentage NUMERIC(5, 2) DEFAULT 100.00,
    coverage_type VARCHAR(255) DEFAULT 'Full',
    is_active BOOLEAN DEFAULT TRUE,
    notes TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (insurance_company_id) REFERENCES insurance_companies(id) ON DELETE SET NULL,
    FOREIGN KEY (corporate_client_id) REFERENCES corporate_clients(id) ON DELETE SET NULL
);

-- employee_insurance_records
CREATE TABLE employee_insurance_records (
    id BIGSERIAL PRIMARY KEY,
    patient_id BIGINT NOT NULL,
    policy_id BIGINT NOT NULL,
    kebele_id VARCHAR(255) NULL,
    woreda VARCHAR(255) NULL,
    region VARCHAR(255) NULL,
    federal_id VARCHAR(255) NULL,
    ministry_department VARCHAR(255) NULL,
    employee_id_number VARCHAR(255) NULL,
    verified BOOLEAN DEFAULT FALSE,
    verified_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
    FOREIGN KEY (policy_id) REFERENCES insurance_policies(id) ON DELETE CASCADE
);

-- insurance_claims
CREATE TABLE insurance_claims (
    id BIGSERIAL PRIMARY KEY,
    patient_id BIGINT NOT NULL,
    invoice_id BIGINT NULL,
    insurance_company_id BIGINT NULL,
    policy_id BIGINT NULL,
    claim_status VARCHAR(255) DEFAULT 'Submitted',
    coverage_amount NUMERIC(10, 2) NULL,
    paid_amount NUMERIC(10, 2) DEFAULT 0,
    submitted_at TIMESTAMP NULL,
    processed_at TIMESTAMP NULL,
    email_sent_at TIMESTAMP NULL,
    email_status VARCHAR(255) NULL,
    payment_due_date DATE NULL,
    payment_received_at TIMESTAMP NULL,
    payment_method VARCHAR(255) NULL,
    reimbursement_required BOOLEAN DEFAULT FALSE,
    receipt_number VARCHAR(255) NULL,
    is_pre_authorized BOOLEAN DEFAULT FALSE,
    pre_authorization_code VARCHAR(255) NULL,
    denial_reason TEXT NULL,
    translated_notes TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
    FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE SET NULL,
    FOREIGN KEY (insurance_company_id) REFERENCES insurance_companies(id) ON DELETE SET NULL,
    FOREIGN KEY (policy_id) REFERENCES insurance_policies(id) ON DELETE SET NULL
);
```

### 7. Financial & Calendar

```sql
-- exchange_rates
CREATE TABLE exchange_rates (
    id BIGSERIAL PRIMARY KEY,
    currency_code VARCHAR(10) NOT NULL,
    rate_to_etb NUMERIC(10, 4) NOT NULL,
    source VARCHAR(255) NULL,
    date_effective DATE NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

-- ethiopian_calendar_days
CREATE TABLE ethiopian_calendar_days (
    id BIGSERIAL PRIMARY KEY,
    gregorian_date DATE NOT NULL,
    ethiopian_date VARCHAR(255) NULL,
    description TEXT NULL,
    is_holiday BOOLEAN DEFAULT FALSE,
    region VARCHAR(255) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

### 8. Visit & Service Management

```sql
-- services
CREATE TABLE services (
    id BIGSERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT NULL,
    category VARCHAR(100) NOT NULL,
    price NUMERIC(10, 2) NOT NULL,
    duration INTEGER NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

-- visit_services
CREATE TABLE visit_services (
    id BIGSERIAL PRIMARY KEY,
    patient_id BIGINT NOT NULL,
    staff_id BIGINT NULL,
    assignment_id BIGINT NULL,
    event_id BIGINT NULL,
    scheduled_at TIMESTAMP NULL,
    check_in_time TIMESTAMP NULL,
    check_in_latitude NUMERIC(10, 8) NULL,
    check_in_longitude NUMERIC(11, 8) NULL,
    check_out_time TIMESTAMP NULL,
    check_out_latitude NUMERIC(10, 8) NULL,
    check_out_longitude NUMERIC(11, 8) NULL,
    visit_notes TEXT NULL,
    service_id BIGINT NULL,
    prescription_file VARCHAR(255) NULL,
    vitals_file VARCHAR(255) NULL,
    status VARCHAR(255) DEFAULT 'Pending',
    cost NUMERIC(10, 2) NULL,
    is_paid_to_staff BOOLEAN DEFAULT FALSE,
    is_invoiced BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
    FOREIGN KEY (staff_id) REFERENCES staff(id) ON DELETE CASCADE,
    FOREIGN KEY (assignment_id) REFERENCES caregiver_assignments(id) ON DELETE SET NULL,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE SET NULL,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE SET NULL
);

-- invoice_items
CREATE TABLE invoice_items (
    id BIGSERIAL PRIMARY KEY,
    invoice_id BIGINT NOT NULL,
    visit_service_id BIGINT NOT NULL,
    description VARCHAR(255) NOT NULL,
    cost NUMERIC(10, 2) NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE CASCADE,
    FOREIGN KEY (visit_service_id) REFERENCES visit_services(id) ON DELETE CASCADE
);

-- invoices
CREATE TABLE invoices (
    id BIGSERIAL PRIMARY KEY,
    patient_id BIGINT NOT NULL,
    service_id BIGINT NOT NULL, -- This seems to be a foreign key to visit_services, but named service_id
    invoice_number VARCHAR(255) UNIQUE NOT NULL,
    invoice_date DATE NOT NULL,
    due_date DATE NOT NULL,
    subtotal NUMERIC(10, 2) NOT NULL,
    tax_amount NUMERIC(10, 2) DEFAULT 0.00,
    grand_total NUMERIC(10, 2) NOT NULL,
    amount NUMERIC(10, 2) NOT NULL,
    status VARCHAR(255) DEFAULT 'Pending',
    paid_at TIMESTAMP NULL,
    insurance_company_id BIGINT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
    FOREIGN KEY (service_id) REFERENCES visit_services(id) ON DELETE CASCADE,
    FOREIGN KEY (insurance_company_id) REFERENCES insurance_companies(id) ON DELETE SET NULL
);

-- payout_visit_service (pivot table)
CREATE TABLE payout_visit_service (
    staff_payout_id BIGINT NOT NULL,
    visit_service_id BIGINT NOT NULL,
    PRIMARY KEY (staff_payout_id, visit_service_id),
    FOREIGN KEY (staff_payout_id) REFERENCES staff_payouts(id) ON DELETE CASCADE,
    FOREIGN KEY (visit_service_id) REFERENCES visit_services(id) ON DELETE CASCADE
);

