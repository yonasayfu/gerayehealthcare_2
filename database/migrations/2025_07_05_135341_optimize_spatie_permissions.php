<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Optimize Spatie permissions and roles tables for better performance
     */
    public function up(): void
    {
        // Optimize roles table
        Schema::table('roles', function (Blueprint $table) {
            $table->index('name', 'idx_roles_name');
            $table->index('guard_name', 'idx_roles_guard_name');
            $table->index(['name', 'guard_name'], 'idx_roles_name_guard');
        });

        // Optimize permissions table
        Schema::table('permissions', function (Blueprint $table) {
            $table->index('name', 'idx_permissions_name');
            $table->index('guard_name', 'idx_permissions_guard_name');
            $table->index(['name', 'guard_name'], 'idx_permissions_name_guard');
        });

        // Optimize model_has_permissions table
        Schema::table('model_has_permissions', function (Blueprint $table) {
            $table->index('model_id', 'idx_model_has_permissions_model_id');
            $table->index('model_type', 'idx_model_has_permissions_model_type');
            $table->index('permission_id', 'idx_model_has_permissions_permission_id');
            $table->index(['model_id', 'model_type'], 'idx_model_has_permissions_model');
            $table->index(['model_id', 'model_type', 'permission_id'], 'idx_model_has_permissions_full');
        });

        // Optimize model_has_roles table
        Schema::table('model_has_roles', function (Blueprint $table) {
            $table->index('model_id', 'idx_model_has_roles_model_id');
            $table->index('model_type', 'idx_model_has_roles_model_type');
            $table->index('role_id', 'idx_model_has_roles_role_id');
            $table->index(['model_id', 'model_type'], 'idx_model_has_roles_model');
            $table->index(['model_id', 'model_type', 'role_id'], 'idx_model_has_roles_full');
        });

        // Optimize role_has_permissions table
        Schema::table('role_has_permissions', function (Blueprint $table) {
            $table->index('role_id', 'idx_role_has_permissions_role_id');
            $table->index('permission_id', 'idx_role_has_permissions_permission_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('role_has_permissions', function (Blueprint $table) {
            $table->dropIndex('idx_role_has_permissions_role_id');
            $table->dropIndex('idx_role_has_permissions_permission_id');
        });

        Schema::table('model_has_roles', function (Blueprint $table) {
            $table->dropIndex('idx_model_has_roles_model_id');
            $table->dropIndex('idx_model_has_roles_model_type');
            $table->dropIndex('idx_model_has_roles_role_id');
            $table->dropIndex('idx_model_has_roles_model');
            $table->dropIndex('idx_model_has_roles_full');
        });

        Schema::table('model_has_permissions', function (Blueprint $table) {
            $table->dropIndex('idx_model_has_permissions_model_id');
            $table->dropIndex('idx_model_has_permissions_model_type');
            $table->dropIndex('idx_model_has_permissions_permission_id');
            $table->dropIndex('idx_model_has_permissions_model');
            $table->dropIndex('idx_model_has_permissions_full');
        });

        Schema::table('permissions', function (Blueprint $table) {
            $table->dropIndex('idx_permissions_name');
            $table->dropIndex('idx_permissions_guard_name');
            $table->dropIndex('idx_permissions_name_guard');
        });

        Schema::table('roles', function (Blueprint $table) {
            $table->dropIndex('idx_roles_name');
            $table->dropIndex('idx_roles_guard_name');
            $table->dropIndex('idx_roles_name_guard');
        });
    }
};
