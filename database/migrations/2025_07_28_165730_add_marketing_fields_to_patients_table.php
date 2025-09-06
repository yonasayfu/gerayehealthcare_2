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
            // Add marketing tracking fields to the existing patients table
            $table->foreignId('acquisition_source_id')->nullable()->constrained('lead_sources')->onDelete('set null'); // FK to lead_sources
            // Reference the base marketing_campaigns table
            $table->foreignId('marketing_campaign_id')->nullable()->constrained('marketing_campaigns')->onDelete('set null'); // FK to marketing_campaigns
            $table->string('utm_campaign')->nullable();
            $table->string('utm_source')->nullable();
            $table->string('utm_medium')->nullable();
            $table->foreignId('lead_id')->nullable()->constrained('marketing_leads')->onDelete('set null'); // FK to marketing_leads
            $table->decimal('acquisition_cost', 10, 2)->nullable(); // Cost to acquire this patient via marketing
            $table->timestamp('acquisition_date')->nullable(); // When the patient was acquired
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropForeign(['acquisition_source_id']);
            $table->dropForeign(['marketing_campaign_id']);
            $table->dropForeign(['lead_id']);

            $table->dropColumn([
                'acquisition_source_id',
                'marketing_campaign_id',
                'utm_campaign',
                'utm_source',
                'utm_medium',
                'lead_id',
                'acquisition_cost',
                'acquisition_date',
            ]);
        });
    }
};
