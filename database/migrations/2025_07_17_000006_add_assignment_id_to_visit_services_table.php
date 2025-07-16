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
        Schema::table('visit_services', function (Blueprint $table) {
            // This adds the column and links it to the 'caregiver_assignments' table.
            // It's nullable() because a visit might be a one-off task without a long-term assignment.
            // onDelete('set null') means if the master assignment is deleted, this visit record is kept.
            $table->foreignId('assignment_id')
                  ->nullable()
                  ->after('staff_id')
                  ->constrained('caregiver_assignments')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visit_services', function (Blueprint $table) {
            // This is the reverse operation for safety
            $table->dropForeign(['assignment_id']);
            $table->dropColumn('assignment_id');
        });
    }
};