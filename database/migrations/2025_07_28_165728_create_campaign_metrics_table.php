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
        Schema::create('campaign_metrics', function (Blueprint $table) {
            $table->id();
            // Reference the base marketing_campaigns table created earlier
            $table->foreignId('campaign_id')->constrained('marketing_campaigns')->onDelete('cascade');
            $table->date('date');
            $table->integer('impressions')->default(0);
            $table->integer('clicks')->default(0);
            $table->integer('conversions')->default(0);
            $table->decimal('cost_per_click', 10, 4)->nullable();
            $table->decimal('cost_per_conversion', 10, 2)->nullable();
            $table->decimal('roi_percentage', 8, 2)->nullable();
            $table->integer('leads_generated')->default(0);
            $table->integer('patients_acquired')->default(0);
            $table->decimal('revenue_generated', 15, 2)->default(0);
            $table->decimal('engagement_rate', 5, 2)->nullable(); // e.g., percentage
            $table->integer('reach')->nullable();
            $table->timestamps();

            // Optional: Add index for performance on frequently queried columns
            $table->index(['campaign_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaign_metrics');
    }
};