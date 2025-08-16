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
        Schema::table('inventory_maintenance_records', function (Blueprint $table) {
            // Add the new foreign key column
            $table->foreignId('performed_by_staff_id')->nullable()->constrained('staff')->onDelete('set null')->after('description');
            
            // Drop the old string column
            $table->dropColumn('performed_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory_maintenance_records', function (Blueprint $table) {
            // Re-add the old string column
            $table->string('performed_by')->nullable()->after('description');

            // Drop the foreign key column
            $table->dropConstrainedForeignId('performed_by_staff_id');
        });
    }
};
