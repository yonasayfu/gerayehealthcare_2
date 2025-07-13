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
                  ->nullable() // Allow null if a patient might not be associated with a staff, or for existing data
                  ->constrained('staff') // Reference the 'id' column on the 'staff' table
                  ->onDelete('set null'); // If staff member is deleted, set this field to null

            // Add foreign key for the caregiver who registered the patient
            // Assumes a 'caregivers' table exists. Adjust 'caregivers' if your table name is different.
            $table->foreignId('registered_by_caregiver_id')
                  ->nullable() // Allow null if a patient might not be associated with a caregiver
                  ->constrained('caregivers') // Reference the 'id' column on the 'caregivers' table
                  ->onDelete('set null'); // If caregiver is deleted, set this field to null

            // The positions for registered_by_staff_id and registered_by_caregiver_id can be flexible.
            // If you want them after 'geolocation', you could use:
            // $table->after('geolocation', function (Blueprint $table) {
            //     $table->foreignId('registered_by_staff_id')->nullable()->constrained('staff')->onDelete('set null');
            //     $table->foreignId('registered_by_caregiver_id')->nullable()->constrained('caregivers')->onDelete('set null');
            // });
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