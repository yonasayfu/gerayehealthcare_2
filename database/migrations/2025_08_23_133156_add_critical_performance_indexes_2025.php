<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This migration adds critical performance indexes for the most
     * frequently queried columns to improve application performance.
     */
    public function up(): void
    {
        // Patient table optimizations
        Schema::table('patients', function (Blueprint $table) {
            $table->index('phone_number', 'idx_patients_phone_number');
            $table->index('email', 'idx_patients_email');
            $table->index('full_name', 'idx_patients_full_name');
            $table->index('gender', 'idx_patients_gender');
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
            $table->index('user_id', 'idx_staff_user_id');
        });

        // Invoice table optimizations
        Schema::table('invoices', function (Blueprint $table) {
            $table->index('patient_id', 'idx_invoices_patient');
            $table->index('status', 'idx_invoices_status');
            $table->index(['patient_id', 'status'], 'idx_invoices_patient_status');
        });

        // Visit Services table optimizations
        Schema::table('visit_services', function (Blueprint $table) {
            $table->index('patient_id', 'idx_visit_services_patient');
            $table->index('staff_id', 'idx_visit_services_staff');
            $table->index('status', 'idx_visit_services_status');
            $table->index('scheduled_at', 'idx_visit_services_scheduled');
            $table->index(['patient_id', 'status'], 'idx_visit_services_patient_status');
        });

        // Users table optimizations (for authentication performance)
        Schema::table('users', function (Blueprint $table) {
            $table->index('email', 'idx_users_email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('idx_users_email');
        });

        Schema::table('visit_services', function (Blueprint $table) {
            $table->dropIndex('idx_visit_services_patient');
            $table->dropIndex('idx_visit_services_staff');
            $table->dropIndex('idx_visit_services_status');
            $table->dropIndex('idx_visit_services_scheduled');
            $table->dropIndex('idx_visit_services_patient_status');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropIndex('idx_invoices_patient');
            $table->dropIndex('idx_invoices_status');
            $table->dropIndex('idx_invoices_patient_status');
        });

        Schema::table('staff', function (Blueprint $table) {
            $table->dropIndex('idx_staff_email');
            $table->dropIndex('idx_staff_phone');
            $table->dropIndex('idx_staff_full_name');
            $table->dropIndex('idx_staff_position');
            $table->dropIndex('idx_staff_department');
            $table->dropIndex('idx_staff_status');
            $table->dropIndex('idx_staff_user_id');
        });

        Schema::table('patients', function (Blueprint $table) {
            $table->dropIndex('idx_patients_phone_number');
            $table->dropIndex('idx_patients_email');
            $table->dropIndex('idx_patients_full_name');
            $table->dropIndex('idx_patients_gender');
            $table->dropIndex('idx_patients_timestamps');
        });
    }
};
