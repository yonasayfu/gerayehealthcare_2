# GerayeHealthcare ERD (Phase 1 snapshot)

This is a high-level ERD overview assembled from the current Laravel migrations. It focuses on core entities and their key relationships. Use this as a guide for reviews and to validate referential integrity decisions.

Note: “FK” lists the column and the referenced table. Many FKs include onDelete behaviors (cascade, set null) per migrations.

Core
- users
  - id, name, email, password, ...
  - Has roles/permissions via spatie tables
- staff
  - id, user_id? (via business logic), role, hourly_rate, ...
  - FKs: none mandatory except linked in other tables
- patients
  - id, user_id (nullable), fayda_id, source, patient_code, registered_by_staff_id (nullable), ...
  - FKs: registered_by_staff_id -> staff (set null), user_id -> users (nullable)
- caregiver_assignments
  - id, patient_id, staff_id, status, shift_start, shift_end, ...
  - FKs: patient_id -> patients (cascade), staff_id -> staff (cascade)
- visit_services
  - id, patient_id, staff_id, caregiver_assignment_id (nullable), service_id (nullable), event_id (nullable)
  - invoicing, gps, cost fields
  - FKs: patient_id -> patients, staff_id -> staff, caregiver_assignment_id -> caregiver_assignments (set null), service_id -> services (set null), event_id -> events (set null)

Billing
- invoices
  - id, patient_id (nullable), status, grand_total, paid_at, insurance_company_id (nullable)
  - FKs: patient_id -> patients (set null), insurance_company_id -> insurance_companies (set null)
- invoice_items
  - id, invoice_id, visit_service_id (nullable), description, amount, qty
  - FKs: invoice_id -> invoices (cascade), visit_service_id -> visit_services (set null)
- shared_invoices
  - id, invoice_id, share_token, expires_at, ...
  - FK: invoice_id -> invoices (cascade)

Inventory
- inventory_items
  - id, name, sku, quantity_on_hand, reorder_level, ...
- suppliers
  - id, name, ...
- inventory_requests
  - id, requester_id (nullable), approver_id (nullable), item_id, quantity_requested, status (default Pending), priority (default Normal), needed_by_date, ...
  - FKs: requester_id -> staff (set null), approver_id -> staff (set null), item_id -> inventory_items (cascade)
- inventory_transactions
  - id, item_id, quantity, transaction_type, performed_by_staff_id (nullable)
  - FKs: item_id -> inventory_items (cascade), performed_by_staff_id -> staff (set null)
- inventory_maintenance_records
  - id, inventory_item_id, performed_by_staff_id (nullable), ...
  - FKs: inventory_item_id -> inventory_items (cascade), performed_by_staff_id -> staff (set null)
- inventory_alerts
  - id, inventory_item_id, delegated_task_id (nullable), due_date (nullable), ...
  - FKs: inventory_item_id -> inventory_items (cascade)

Insurance
- insurance_companies
- corporate_clients
- insurance_policies
  - id, insurance_company_id, corporate_client_id, coverage fields, ...
  - FKs: insurance_company_id -> insurance_companies (cascade), corporate_client_id -> corporate_clients (cascade)
- employee_insurance_records
  - id, patient_id, insurance_policy_id, ...
  - FKs: patient_id -> patients (cascade), insurance_policy_id -> insurance_policies (cascade)
- insurance_claims
  - id, invoice_id, insurance_company_id (nullable), policy_id (nullable), status, paid_amount, ...
  - FKs: invoice_id -> invoices (cascade), insurance_company_id -> insurance_companies (set null), policy_id -> insurance_policies (set null)

Marketing
- lead_sources
- marketing_platforms
- marketing_campaigns
  - id, platform_id (nullable), status, urgency, responsible_staff_id (nullable), ...
  - FKs: platform_id -> marketing_platforms (set null), responsible_staff_id -> staff (set null)
- landing_pages
- marketing_leads
  - id, campaign_id (nullable), assigned_staff_id (nullable), patient_id (nullable), ...
  - FKs: campaign_id -> marketing_campaigns (set null), assigned_staff_id -> staff (set null), patient_id -> patients (set null)
- campaign_metrics
- campaign_contents
- marketing_budgets
- marketing_tasks
  - id, campaign_id (nullable), assigned_to_staff_id (nullable), expected_results, ...
  - FKs: campaign_id -> marketing_campaigns (set null), assigned_to_staff_id -> staff (set null)

Medical Records
- medical_visits
  - id, patient_id, staff_id (nullable), ...
  - FKs: patient_id -> patients (cascade), staff_id -> staff (set null)
- medical_documents
  - id, patient_id, created_by_staff_id (nullable), ...
  - FKs: patient_id -> patients (cascade), created_by_staff_id -> staff (set null)
- prescriptions
  - id, patient_id, prescribed_by_staff_id (nullable), ...
  - FKs: patient_id -> patients (cascade), prescribed_by_staff_id -> staff (set null)
- prescription_items
  - id, prescription_id,
  - FK: prescription_id -> prescriptions (cascade)

Events & Eligibility
- events
- event_recommendations (event_id, patient_id)
- eligibility_criteria (event_id)
- event_staff_assignments (event_id, staff_id)
- event_broadcasts (event_id)
- event_participants (event_id, patient_id)

Messaging & Groups
- messages
  - id, sender_id, receiver_id, reply_to_id (nullable), ...
  - FKs: sender_id -> users, receiver_id -> users, reply_to_id -> messages (set null)
- notifications (standard Laravel)
- groups, group_members, group_messages
- reactions (reactable morph to messages/group_messages)

Analytics Views (DB views)
- service_volume_view, revenue_ar_view, marketing_roi_view (materialized via migrations)

Notes
- Soft deletes: present on some models/tables (confirm per model definitions as needed)
- Indices: Added across critical tables (see *_add_indexes_* migrations) to support search/filter/export use cases
- OnDelete behaviors:
  - set null used for “ownership” links to users/staff/patients where records should remain but the relation can be cleared
  - cascade used for strict parent-child (e.g., invoice -> items)

This ERD is a living document; update in future phases as relationships evolve.

