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
        // Create the base table structure as per DATABASE_SCHEMA.md
        // This will be enhanced later.
        Schema::create('marketing_campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('platform')->nullable(); // e.g., TikTok, Meta, Google (Original schema)
            $table->string('campaign_name')->nullable();
            $table->text('landing_page_url')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->text('roi_report_url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marketing_campaigns');
    }
};