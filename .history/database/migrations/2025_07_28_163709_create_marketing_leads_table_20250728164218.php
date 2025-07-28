// database/migrations/xxxx_xx_xx_xxxxxx_create_marketing_leads_table.php

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
        Schema::create('marketing_leads', function (Blueprint $table) {
            $table->id();
            $table->string('lead_code')->unique(); // Auto-generated like LEAD-00001
            $table->foreignId('source_campaign_id')->nullable()->constrained('marketing_campaigns')->onDelete('set null'); // FK to marketing_campaigns
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('country')->nullable();
            $table->string('utm_source')->nullable();
            $table->string('utm_campaign')->nullable();
            $table->string('utm_medium')->nullable();
            $table->foreignId('landing_page_id')->nullable()->constrained('landing_pages')->onDelete('set null'); // FK to landing_pages
            $table->integer('lead_score')->default(0); // Lead scoring system
            $table->string('status')->default('New'); // New, Contacted, Qualified, Converted, Lost
            $table->foreignId('assigned_staff_id')->nullable()->constrained('staff')->onDelete('set null'); // FK to staff
            $table->foreignId('converted_patient_id')->nullable()->constrained('patients')->onDelete('set null'); // FK to patients (link to converted patient)
            $table->timestamp('conversion_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            // Optional indexes for common lookups
            $table->index(['source_campaign_id']);
            $table->index(['status']);
            $table->index(['converted_patient_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marketing_leads');
    }
};