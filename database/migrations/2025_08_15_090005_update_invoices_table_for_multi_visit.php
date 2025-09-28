<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            // Drop service_id if it exists (moving to multi-visit via invoice_items)
            if (Schema::hasColumn('invoices', 'service_id')) {
                // Drop foreign key constraint before dropping column
                try {
                    $table->dropForeign(['service_id']);
                } catch (\Throwable $e) {
                    // In case constraint name differs; ignore to attempt column drop
                }
                $table->dropColumn('service_id');
            }

            // Add insurance_company_id if missing
            if (! Schema::hasColumn('invoices', 'insurance_company_id')) {
                $table->foreignId('insurance_company_id')
                    ->nullable()
                    ->constrained('insurance_companies')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            // Re-add service_id (nullable on down just in case)
            if (! Schema::hasColumn('invoices', 'service_id')) {
                $table->foreignId('service_id')->nullable()->constrained('visit_services')->nullOnDelete();
            }

            // Drop insurance_company_id
            if (Schema::hasColumn('invoices', 'insurance_company_id')) {
                // No need to drop foreign key here, as it's handled by 2025_07_31_225542
                $table->dropColumn('insurance_company_id');
            }
        });
    }
};
