// database/migrations/xxxx_xx_xx_xxxxxx_enhance_marketing_campaigns_table.php

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
            // Drop old columns (assuming they exist from the original simple schema)
            // Adjust column names if they differ in your existing table
            $table->dropColumn(['platform', 'landing_page_url', 'roi_report_url']);
            // Note: Dropping columns might require 'doctrine/dbal' package: composer require doctrine/dbal

            // Add new columns for enhanced schema
            $table->string('campaign_code')->unique(); // Auto-generated like CAM-00001
            $table->foreignId('platform_id')->constrained('marketing_platforms')->onDelete('cascade'); // FK to marketing_platforms
            $table->string('campaign_name');
            $table->string('campaign_type')->nullable(); // e.g., Awareness, Lead Gen, Conversion
            $table->json('target_audience')->nullable(); // JSON: demographics, interests, locations
            $table->decimal('budget_allocated', 15, 2)->default(0);
            $table->decimal('budget_spent', 15, 2)->default(0);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('status')->default('Draft'); // Draft, Active, Paused, Completed
            $table->string('landing_page_url')->nullable();
            $table->string('utm_campaign')->nullable();
            $table->string('utm_source')->nullable();
            $table->string('utm_medium')->nullable();
            $table->foreignId('assigned_staff_id')->nullable()->constrained('staff')->onDelete('set null'); // FK to staff
            $table->foreignId('created_by_staff_id')->nullable()->constrained('staff')->onDelete('set null'); // FK to staff
            $table->json('goals')->nullable(); // JSON: leads, conversions, awareness targets

            // Ensure created_at and updated_at exist (they usually do)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('marketing_campaigns', function (Blueprint $table) {
            // Re-add old columns structure if needed for rollback
            // This is a simplified reversal, adjust as needed
             $table->string('platform')->nullable();
             $table->text('landing_page_url')->nullable();
             $table->text('roi_report_url')->nullable();

            // Drop new columns
            $table->dropForeign(['platform_id']);
            $table->dropForeign(['assigned_staff_id']);
            $table->dropForeign(['created_by_staff_id']);
            $table->dropColumn([
                'campaign_code', 'platform_id', 'campaign_name', 'campaign_type',
                'target_audience', 'budget_allocated', 'budget_spent', 'start_date',
                'end_date', 'status', 'landing_page_url', 'utm_campaign',
                'utm_source', 'utm_medium', 'assigned_staff_id', 'created_by_staff_id', 'goals'
            ]);
        });
    }
};