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
            // Add the new foreign key column first. It's nullable for now.
            $table->foreignId('service_id')->nullable()->after('visit_notes')->constrained('services')->onDelete('set null');

            // Now, safely drop the old text column.
            $table->dropColumn('service_description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visit_services', function (Blueprint $table) {
            // Add the old column back if we ever need to reverse this migration.
            $table->string('service_description')->nullable()->after('visit_notes');

            // Drop the foreign key and the column.
            $table->dropForeign(['service_id']);
            $table->dropColumn('service_id');
        });
    }
};
