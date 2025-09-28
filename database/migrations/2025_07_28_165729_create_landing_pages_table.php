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
        Schema::create('landing_pages', function (Blueprint $table) {
            $table->id();
            $table->string('page_code')->unique(); // Auto-generated like LP-00001
            // Reference the base marketing_campaigns table
            $table->foreignId('campaign_id')->nullable()->constrained('marketing_campaigns')->onDelete('set null');
            $table->string('page_title');
            $table->string('page_url')->unique();
            $table->string('template_used')->nullable();
            $table->string('language')->default('en'); // Multi-language support
            $table->json('form_fields')->nullable(); // JSON definition of form fields
            $table->string('conversion_goal')->nullable(); // e.g., Form Submission, Call
            $table->integer('views')->default(0);
            $table->integer('submissions')->default(0);
            $table->decimal('conversion_rate', 5, 2)->nullable(); // Calculated field, stored for performance
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('landing_pages');
    }
};
