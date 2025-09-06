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
        Schema::table('marketing_campaigns', function (Blueprint $table) {
            // Drop old columns from the base schema (they will be replaced or are no longer needed in enhanced version)
            // Note: Dropping columns might require 'doctrine/dbal' package: composer require doctrine/dbal
            // If dropping causes issues, comment out or handle differently.
            $table->dropColumn(['platform', 'landing_page_url', 'roi_report_url']);

            // Add new columns for enhanced schema
            $table->string('campaign_code')->unique()->after('id'); // Auto-generated like CAM-00001
            $table->foreignId('platform_id')->constrained('marketing_platforms')->onDelete('cascade')->after('campaign_code'); // FK to marketing_platforms
            // campaign_name already exists, keep it.
            $table->string('campaign_type')->nullable()->after('campaign_name'); // e.g., Awareness, Lead Gen, Conversion
            $table->json('target_audience')->nullable()->after('campaign_type'); // JSON: demographics, interests, locations
            $table->decimal('budget_allocated', 15, 2)->default(0)->after('target_audience');
            $table->decimal('budget_spent', 15, 2)->default(0)->after('budget_allocated');
            // start_date and end_date already exist, keep them.
            $table->string('status')->default('Draft')->after('end_date'); // Draft, Active, Paused, Completed
            // landing_page_url is removed, but might be implied by the landing_pages table relationship. Add if needed differently.
            $table->string('utm_campaign')->nullable()->after('status');
            $table->string('utm_source')->nullable()->after('utm_campaign');
            $table->string('utm_medium')->nullable()->after('utm_source');
            $table->foreignId('assigned_staff_id')->nullable()->constrained('staff')->onDelete('set null')->after('utm_medium'); // FK to staff
            $table->foreignId('created_by_staff_id')->nullable()->constrained('staff')->onDelete('set null')->after('assigned_staff_id'); // FK to staff
            $table->json('goals')->nullable()->after('created_by_staff_id'); // JSON: leads, conversions, awareness targets

            // Ensure created_at and updated_at exist (they usually do)
            // $table->timestamps(); // Not needed in table() as they are already present from initial creation.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('marketing_campaigns', function (Blueprint $table) {
            // Re-add old columns structure if needed for rollback
            // This is a simplified reversal, adjust column order if necessary
            $table->string('platform')->nullable()->after('campaign_name');
            $table->text('landing_page_url')->nullable()->after('platform');
            $table->text('roi_report_url')->nullable()->after('landing_page_url');

            // Drop new columns added in up()
            $table->dropForeign(['platform_id']);
            $table->dropForeign(['assigned_staff_id']);
            $table->dropForeign(['created_by_staff_id']);
            $table->dropColumn([
                'campaign_code', 'platform_id', 'campaign_type',
                'target_audience', 'budget_allocated', 'budget_spent',
                'status', 'utm_campaign', 'utm_source', 'utm_medium',
                'assigned_staff_id', 'created_by_staff_id', 'goals',
            ]);
        });
    }
};
