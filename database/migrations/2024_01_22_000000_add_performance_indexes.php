<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add indexes for better performance on role/permission queries
        Schema::table('model_has_roles', function (Blueprint $table) {
            $table->index(['model_id', 'model_type'], 'model_has_roles_model_idx');
        });

        Schema::table('model_has_permissions', function (Blueprint $table) {
            $table->index(['model_id', 'model_type'], 'model_has_permissions_model_idx');
        });

        Schema::table('role_has_permissions', function (Blueprint $table) {
            $table->index(['role_id'], 'role_has_permissions_role_idx');
        });

        // Add indexes for frequently queried columns
        Schema::table('patients', function (Blueprint $table) {
            $table->index(['full_name'], 'patients_name_idx');
            $table->index(['phone_number'], 'patients_phone_idx');
        });

        Schema::table('staff', function (Blueprint $table) {
            $table->index(['first_name', 'last_name'], 'staff_name_idx');
            $table->index(['email'], 'staff_email_idx');
        });

        Schema::table('inventory_items', function (Blueprint $table) {
            $table->index(['name'], 'inventory_items_name_idx');
            $table->index(['status'], 'inventory_items_status_idx');
        });

        Schema::table('caregiver_assignments', function (Blueprint $table) {
            $table->index(['staff_id', 'patient_id'], 'assignments_staff_patient_idx');
            $table->index(['status'], 'assignments_status_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('model_has_roles', function (Blueprint $table) {
            $table->dropIndex('model_has_roles_model_idx');
        });

        Schema::table('model_has_permissions', function (Blueprint $table) {
            $table->dropIndex('model_has_permissions_model_idx');
        });

        Schema::table('role_has_permissions', function (Blueprint $table) {
            $table->dropIndex('role_has_permissions_role_idx');
        });

        Schema::table('patients', function (Blueprint $table) {
            $table->dropIndex('patients_name_idx');
            $table->dropIndex('patients_phone_idx');
        });

        Schema::table('staff', function (Blueprint $table) {
            $table->dropIndex('staff_name_idx');
            $table->dropIndex('staff_email_idx');
        });

        Schema::table('inventory_items', function (Blueprint $table) {
            $table->dropIndex('inventory_items_name_idx');
            $table->dropIndex('inventory_items_status_idx');
        });

        Schema::table('caregiver_assignments', function (Blueprint $table) {
            $table->dropIndex('assignments_staff_patient_idx');
            $table->dropIndex('assignments_status_idx');
        });
    }
};