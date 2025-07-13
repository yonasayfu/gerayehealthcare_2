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
        Schema::table('patients', function (Blueprint $table) {
            // Ensure 'email' column exists if it doesn't already, as it's added to fillable
            // If you already have this column, you can remove this block.
            if (!Schema::hasColumn('patients', 'email')) {
                $table->string('email')->nullable()->after('phone_number');
            }

            // Add foreign key for the staff member who registered the patient
            // Assumes a 'staff' table exists. Adjust 'staff' if your table name is different.
            $table->foreignId('registered_by_staff_id')
                  ->nullable()
                  ->constrained('staff') // This line assumes your staff table is named 'staff'
                  ->onDelete('set null');

            // Add foreign key for the caregiver who registered the patient
            // CORRECTED: Referencing 'caregiver_assignments' table
            $table->foreignId('registered_by_caregiver_id')
                  ->nullable()
                  ->constrained('caregiver_assignments') // <--- CORRECTED LINE HERE
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            // Drop foreign key constraints first
            $table->dropConstrainedForeignId('registered_by_staff_id');
            $table->dropConstrainedForeignId('registered_by_caregiver_id');
            // Then drop the columns
            $table->dropColumn(['registered_by_staff_id', 'registered_by_caregiver_id']);

            // If you added the email column in this migration, drop it as well
            if (Schema::hasColumn('patients', 'email')) {
                 $table->dropColumn('email');
            }
        });
    }
};