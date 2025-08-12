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
        Schema::table('staff_availabilities', function (Blueprint $table) {
            // Improve lookup and overlap query performance
            if (!Schema::hasColumn('staff_availabilities', 'staff_id')) {
                // safety: in case of divergent schema
                $table->unsignedBigInteger('staff_id')->index('idx_staff_availabilities_staff_id');
            } else {
                $table->index('staff_id', 'idx_staff_availabilities_staff_id');
            }

            // Composite indexes for common queries
            $table->index(['staff_id', 'start_time'], 'idx_staff_availabilities_staff_start');
            $table->index(['start_time', 'end_time'], 'idx_staff_availabilities_start_end');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('staff_availabilities', function (Blueprint $table) {
            $table->dropIndex('idx_staff_availabilities_staff_id');
            $table->dropIndex('idx_staff_availabilities_staff_start');
            $table->dropIndex('idx_staff_availabilities_start_end');
        });
    }
};
