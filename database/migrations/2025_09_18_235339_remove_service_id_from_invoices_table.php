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
        Schema::table('invoices', function (Blueprint $table) {
            if (Schema::hasColumn('invoices', 'service_id')) {
                try {
                    $table->dropForeign(['service_id']);
                } catch (\Throwable $e) {
                    // Ignore if constraint doesn't exist
                }
                $table->dropColumn('service_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            if (!Schema::hasColumn('invoices', 'service_id')) {
                $table->foreignId('service_id')->nullable()->constrained('visit_services')->onDelete('cascade');
            }
        });
    }
};