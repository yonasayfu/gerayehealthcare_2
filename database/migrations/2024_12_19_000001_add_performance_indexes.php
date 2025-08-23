<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This migration adds performance indexes for frequently queried columns
     * across all core tables to optimize Clean Architecture query performance.
     */
    public function up(): void
    {
        // Patient table optimizations
        Schema::table('patients', function (Blueprint $table) {
            $table->index('phone', 'idx_patients_phone');
            $table->index('email', 'idx_patients_email');
            $table->index('full_name', 'idx_patients_full_name');
            $table->index(['gender', 'age'], 'idx_patients_demographics');
            $table->index('emergency_contact_phone', 'idx_patients_emergency_phone');
            $table->index('marketing_campaign_id', 'idx_patients_marketing_campaign');
            $table->index('acquisition_date', 'idx_patients_acquisition_date');
            $table->index(['created_at', 'updated_at'], 'idx_patients_timestamps');
        });

        // Staff table optimizations
        Schema::table('staff', function (Blueprint $table) {
            $table->index('email', 'idx_staff_email');
            $table->index('phone', 'idx_staff_phone');
            $table->index(['first_name', 'last_name'], 'idx_staff_full_name');
            $table->index('position', 'idx_staff_position');
            $table->index('department', 'idx_staff_department');
            $table->index('status', 'idx_staff_status');
            $table->index('hire_date', 'idx_staff_hire_date');
            $table->index('user_id', 'idx_staff_user_id');
        });

        // Invoice table optimizations
        Schema::table('invoices', function (Blueprint $table) {
            $table->index('patient_id', 'idx_invoices_patient');
            $table->index('invoice_number', 'idx_invoices_number');
            $table->index('status', 'idx_invoices_status');
            $table->index('invoice_date', 'idx_invoices_date');
            $table->index('due_date', 'idx_invoices_due_date');
            $table->index(['status', 'due_date'], 'idx_invoices_status_due');
            $table->index('insurance_company_id', 'idx_invoices_insurance');
            $table->index(['patient_id', 'status'], 'idx_invoices_patient_status');
            $table->index(['invoice_date', 'status'], 'idx_invoices_date_status');
        });

        // Inventory Items table optimizations
        Schema::table('inventory_items', function (Blueprint $table) {
            $table->index('name', 'idx_inventory_name');
            $table->index('serial_number', 'idx_inventory_serial');
            $table->index('item_category', 'idx_inventory_category');
            $table->index('item_type', 'idx_inventory_type');
            $table->index('status', 'idx_inventory_status');
            $table->index('supplier_id', 'idx_inventory_supplier');
            $table->index(['quantity_on_hand', 'reorder_level'], 'idx_inventory_stock_levels');
            $table->index('next_maintenance_due', 'idx_inventory_maintenance_due');
            $table->index(['status', 'item_category'], 'idx_inventory_status_category');
        });

        // Marketing Campaigns table optimizations
        Schema::table('marketing_campaigns', function (Blueprint $table) {
            $table->index('platform_id', 'idx_campaigns_platform');
            $table->index('status', 'idx_campaigns_status');
            $table->index('start_date', 'idx_campaigns_start_date');
            $table->index('end_date', 'idx_campaigns_end_date');
            $table->index(['status', 'platform_id'], 'idx_campaigns_status_platform');
            $table->index(['start_date', 'end_date'], 'idx_campaigns_date_range');
        });

        // Marketing Leads table optimizations
        Schema::table('marketing_leads', function (Blueprint $table) {
            $table->index('status', 'idx_leads_status');
            $table->index('utm_source', 'idx_leads_utm_source');
            $table->index('source_campaign_id', 'idx_leads_campaign');
            $table->index('assigned_staff_id', 'idx_leads_assigned_staff');
            $table->index('converted_patient_id', 'idx_leads_converted_patient');
            $table->index(['status', 'assigned_staff_id'], 'idx_leads_status_staff');
            $table->index(['created_at', 'status'], 'idx_leads_created_status');
        });

        // Events table optimizations
        Schema::table('events', function (Blueprint $table) {
            $table->index('event_date', 'idx_events_date');
            $table->index('region', 'idx_events_region');
            $table->index('broadcast_status', 'idx_events_broadcast_status');
            $table->index('is_free_service', 'idx_events_free_service');
            $table->index('created_by_staff_id', 'idx_events_created_by');
            $table->index(['event_date', 'region'], 'idx_events_date_region');
            $table->index(['is_free_service', 'broadcast_status'], 'idx_events_service_status');
        });

        // Event Participants table optimizations
        Schema::table('event_participants', function (Blueprint $table) {
            $table->index('event_id', 'idx_event_participants_event');
            $table->index('patient_id', 'idx_event_participants_patient');
            $table->index('status', 'idx_event_participants_status');
            $table->index(['event_id', 'status'], 'idx_event_participants_event_status');
        });

        // Visit Services table optimizations
        Schema::table('visit_services', function (Blueprint $table) {
            $table->index('patient_id', 'idx_visit_services_patient');
            $table->index('staff_id', 'idx_visit_services_staff');
            $table->index('status', 'idx_visit_services_status');
            $table->index('scheduled_at', 'idx_visit_services_scheduled');
            $table->index('service_type', 'idx_visit_services_type');
            $table->index(['patient_id', 'status'], 'idx_visit_services_patient_status');
            $table->index(['scheduled_at', 'status'], 'idx_visit_services_scheduled_status');
            $table->index(['staff_id', 'scheduled_at'], 'idx_visit_services_staff_scheduled');
        });

        // Insurance Claims table optimizations
        Schema::table('insurance_claims', function (Blueprint $table) {
            $table->index('patient_id', 'idx_insurance_claims_patient');
            $table->index('invoice_id', 'idx_insurance_claims_invoice');
            $table->index('insurance_company_id', 'idx_insurance_claims_company');
            $table->index('claim_status', 'idx_insurance_claims_status');
            $table->index('submitted_at', 'idx_insurance_claims_submitted');
            $table->index(['claim_status', 'submitted_at'], 'idx_insurance_claims_status_submitted');
        });

        // Marketing Tasks table optimizations
        Schema::table('marketing_tasks', function (Blueprint $table) {
            $table->index('assigned_to_staff_id', 'idx_marketing_tasks_staff');
            $table->index('campaign_id', 'idx_marketing_tasks_campaign');
            $table->index('status', 'idx_marketing_tasks_status');
            $table->index('scheduled_at', 'idx_marketing_tasks_scheduled');
            $table->index('completed_at', 'idx_marketing_tasks_completed');
            $table->index(['status', 'scheduled_at'], 'idx_marketing_tasks_status_scheduled');
            $table->index(['assigned_to_staff_id', 'status'], 'idx_marketing_tasks_staff_status');
        });

        // Campaign Metrics table optimizations
        Schema::table('campaign_metrics', function (Blueprint $table) {
            $table->index('campaign_id', 'idx_campaign_metrics_campaign');
            $table->index('date', 'idx_campaign_metrics_date');
            $table->index(['campaign_id', 'date'], 'idx_campaign_metrics_campaign_date');
        });

        // Marketing Budgets table optimizations
        Schema::table('marketing_budgets', function (Blueprint $table) {
            $table->index('campaign_id', 'idx_marketing_budgets_campaign');
            $table->index('platform_id', 'idx_marketing_budgets_platform');
            $table->index('period_start', 'idx_marketing_budgets_period_start');
            $table->index('period_end', 'idx_marketing_budgets_period_end');
            $table->index(['period_start', 'period_end'], 'idx_marketing_budgets_period');
        });

        // Inventory Transactions table optimizations
        Schema::table('inventory_transactions', function (Blueprint $table) {
            $table->index('inventory_item_id', 'idx_inventory_transactions_item');
            $table->index('transaction_type', 'idx_inventory_transactions_type');
            $table->index('transaction_date', 'idx_inventory_transactions_date');
            $table->index('staff_id', 'idx_inventory_transactions_staff');
            $table->index(['inventory_item_id', 'transaction_date'], 'idx_inventory_transactions_item_date');
        });

        // Inventory Alerts table optimizations
        Schema::table('inventory_alerts', function (Blueprint $table) {
            $table->index('inventory_item_id', 'idx_inventory_alerts_item');
            $table->index('alert_type', 'idx_inventory_alerts_type');
            $table->index('priority', 'idx_inventory_alerts_priority');
            $table->index('acknowledged', 'idx_inventory_alerts_acknowledged');
            $table->index(['alert_type', 'acknowledged'], 'idx_inventory_alerts_type_ack');
        });

        // Staff Availability table optimizations
        Schema::table('staff_availability', function (Blueprint $table) {
            $table->index('staff_id', 'idx_staff_availability_staff');
            $table->index('date', 'idx_staff_availability_date');
            $table->index('available', 'idx_staff_availability_available');
            $table->index(['staff_id', 'date'], 'idx_staff_availability_staff_date');
            $table->index(['date', 'available'], 'idx_staff_availability_date_available');
        });

        // Users table optimizations (for authentication performance)
        Schema::table('users', function (Blueprint $table) {
            $table->index('email', 'idx_users_email');
            $table->index('email_verified_at', 'idx_users_email_verified');
            $table->index(['email', 'email_verified_at'], 'idx_users_email_verified_combo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop indexes in reverse order
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('idx_users_email');
            $table->dropIndex('idx_users_email_verified');
            $table->dropIndex('idx_users_email_verified_combo');
        });

        Schema::table('staff_availability', function (Blueprint $table) {
            $table->dropIndex('idx_staff_availability_staff');
            $table->dropIndex('idx_staff_availability_date');
            $table->dropIndex('idx_staff_availability_available');
            $table->dropIndex('idx_staff_availability_staff_date');
            $table->dropIndex('idx_staff_availability_date_available');
        });

        Schema::table('inventory_alerts', function (Blueprint $table) {
            $table->dropIndex('idx_inventory_alerts_item');
            $table->dropIndex('idx_inventory_alerts_type');
            $table->dropIndex('idx_inventory_alerts_priority');
            $table->dropIndex('idx_inventory_alerts_acknowledged');
            $table->dropIndex('idx_inventory_alerts_type_ack');
        });

        Schema::table('inventory_transactions', function (Blueprint $table) {
            $table->dropIndex('idx_inventory_transactions_item');
            $table->dropIndex('idx_inventory_transactions_type');
            $table->dropIndex('idx_inventory_transactions_date');
            $table->dropIndex('idx_inventory_transactions_staff');
            $table->dropIndex('idx_inventory_transactions_item_date');
        });

        Schema::table('marketing_budgets', function (Blueprint $table) {
            $table->dropIndex('idx_marketing_budgets_campaign');
            $table->dropIndex('idx_marketing_budgets_platform');
            $table->dropIndex('idx_marketing_budgets_period_start');
            $table->dropIndex('idx_marketing_budgets_period_end');
            $table->dropIndex('idx_marketing_budgets_period');
        });

        Schema::table('campaign_metrics', function (Blueprint $table) {
            $table->dropIndex('idx_campaign_metrics_campaign');
            $table->dropIndex('idx_campaign_metrics_date');
            $table->dropIndex('idx_campaign_metrics_campaign_date');
        });

        Schema::table('marketing_tasks', function (Blueprint $table) {
            $table->dropIndex('idx_marketing_tasks_staff');
            $table->dropIndex('idx_marketing_tasks_campaign');
            $table->dropIndex('idx_marketing_tasks_status');
            $table->dropIndex('idx_marketing_tasks_scheduled');
            $table->dropIndex('idx_marketing_tasks_completed');
            $table->dropIndex('idx_marketing_tasks_status_scheduled');
            $table->dropIndex('idx_marketing_tasks_staff_status');
        });

        Schema::table('insurance_claims', function (Blueprint $table) {
            $table->dropIndex('idx_insurance_claims_patient');
            $table->dropIndex('idx_insurance_claims_invoice');
            $table->dropIndex('idx_insurance_claims_company');
            $table->dropIndex('idx_insurance_claims_status');
            $table->dropIndex('idx_insurance_claims_submitted');
            $table->dropIndex('idx_insurance_claims_status_submitted');
        });

        Schema::table('visit_services', function (Blueprint $table) {
            $table->dropIndex('idx_visit_services_patient');
            $table->dropIndex('idx_visit_services_staff');
            $table->dropIndex('idx_visit_services_status');
            $table->dropIndex('idx_visit_services_scheduled');
            $table->dropIndex('idx_visit_services_type');
            $table->dropIndex('idx_visit_services_patient_status');
            $table->dropIndex('idx_visit_services_scheduled_status');
            $table->dropIndex('idx_visit_services_staff_scheduled');
        });

        Schema::table('event_participants', function (Blueprint $table) {
            $table->dropIndex('idx_event_participants_event');
            $table->dropIndex('idx_event_participants_patient');
            $table->dropIndex('idx_event_participants_status');
            $table->dropIndex('idx_event_participants_event_status');
        });

        Schema::table('events', function (Blueprint $table) {
            $table->dropIndex('idx_events_date');
            $table->dropIndex('idx_events_region');
            $table->dropIndex('idx_events_broadcast_status');
            $table->dropIndex('idx_events_free_service');
            $table->dropIndex('idx_events_created_by');
            $table->dropIndex('idx_events_date_region');
            $table->dropIndex('idx_events_service_status');
        });

        Schema::table('marketing_leads', function (Blueprint $table) {
            $table->dropIndex('idx_leads_status');
            $table->dropIndex('idx_leads_utm_source');
            $table->dropIndex('idx_leads_campaign');
            $table->dropIndex('idx_leads_assigned_staff');
            $table->dropIndex('idx_leads_converted_patient');
            $table->dropIndex('idx_leads_status_staff');
            $table->dropIndex('idx_leads_created_status');
        });

        Schema::table('marketing_campaigns', function (Blueprint $table) {
            $table->dropIndex('idx_campaigns_platform');
            $table->dropIndex('idx_campaigns_status');
            $table->dropIndex('idx_campaigns_start_date');
            $table->dropIndex('idx_campaigns_end_date');
            $table->dropIndex('idx_campaigns_status_platform');
            $table->dropIndex('idx_campaigns_date_range');
        });

        Schema::table('inventory_items', function (Blueprint $table) {
            $table->dropIndex('idx_inventory_name');
            $table->dropIndex('idx_inventory_serial');
            $table->dropIndex('idx_inventory_category');
            $table->dropIndex('idx_inventory_type');
            $table->dropIndex('idx_inventory_status');
            $table->dropIndex('idx_inventory_supplier');
            $table->dropIndex('idx_inventory_stock_levels');
            $table->dropIndex('idx_inventory_maintenance_due');
            $table->dropIndex('idx_inventory_status_category');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropIndex('idx_invoices_patient');
            $table->dropIndex('idx_invoices_number');
            $table->dropIndex('idx_invoices_status');
            $table->dropIndex('idx_invoices_date');
            $table->dropIndex('idx_invoices_due_date');
            $table->dropIndex('idx_invoices_status_due');
            $table->dropIndex('idx_invoices_insurance');
            $table->dropIndex('idx_invoices_patient_status');
            $table->dropIndex('idx_invoices_date_status');
        });

        Schema::table('staff', function (Blueprint $table) {
            $table->dropIndex('idx_staff_email');
            $table->dropIndex('idx_staff_phone');
            $table->dropIndex('idx_staff_full_name');
            $table->dropIndex('idx_staff_position');
            $table->dropIndex('idx_staff_department');
            $table->dropIndex('idx_staff_status');
            $table->dropIndex('idx_staff_hire_date');
            $table->dropIndex('idx_staff_user_id');
        });

        Schema::table('patients', function (Blueprint $table) {
            $table->dropIndex('idx_patients_phone');
            $table->dropIndex('idx_patients_email');
            $table->dropIndex('idx_patients_full_name');
            $table->dropIndex('idx_patients_demographics');
            $table->dropIndex('idx_patients_emergency_phone');
            $table->dropIndex('idx_patients_marketing_campaign');
            $table->dropIndex('idx_patients_acquisition_date');
            $table->dropIndex('idx_patients_timestamps');
        });
    }
};