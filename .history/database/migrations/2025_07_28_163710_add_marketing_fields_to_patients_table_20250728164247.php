// database/migrations/xxxx_xx_xx_xxxxxx_add_marketing_fields_to_patients_table.php

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
            // Check if columns exist before adding to avoid errors on re-run
            if (!Schema::hasColumn('patients', 'acquisition_source_id')) {
                $table->foreignId('acquisition_source_id')->nullable()->constrained('lead_sources')->onDelete('set null'); // FK to lead_sources
            }
            if (!Schema::hasColumn('patients', 'marketing_campaign_id')) {
                $table->foreignId('marketing_campaign_id')->nullable()->constrained('marketing_campaigns')->onDelete('set null'); // FK to marketing_campaigns
            }
            if (!Schema::hasColumn('patients', 'utm_campaign')) {
                $table->string('utm_campaign')->nullable();
            }
            if (!Schema::hasColumn('patients', 'utm_source')) {
                $table->string('utm_source')->nullable();
            }
            if (!Schema::hasColumn('patients', 'utm_medium')) {
                $table->string('utm_medium')->nullable();
            }
            if (!Schema::hasColumn('patients', 'lead_id')) {
                $table->foreignId('lead_id')->nullable()->constrained('marketing_leads')->onDelete('set null'); // FK to marketing_leads
            }
            if (!Schema::hasColumn('patients', 'acquisition_cost')) {
                $table->decimal('acquisition_cost', 10, 2)->nullable(); // Cost to acquire this patient via marketing
            }
            if (!Schema::hasColumn('patients', 'acquisition_date')) {
                $table->timestamp('acquisition_date')->nullable(); // When the patient was acquired (could be created_at, but explicit is better)
            }
            // Note: The 'source' column already exists in the patients table. This new 'acquisition_source_id'
            // provides a more structured way to track the source via the lead_sources table.
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
                'acquisition_date'
            ]);
        });
    }
};