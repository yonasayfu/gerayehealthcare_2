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
        Schema::create('campaign_contents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->nullable()->constrained('marketing_campaigns')->onDelete('cascade');
            $table->foreignId('platform_id')->nullable()->constrained('marketing_platforms')->onDelete('set null');
            $table->string('content_type'); // e.g., Post, Video, Ad Creative
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('media_url')->nullable(); // Link to image/video
            $table->timestamp('scheduled_post_date')->nullable();
            $table->timestamp('actual_post_date')->nullable();
            $table->string('status')->default('Draft'); // Draft, Scheduled, Posted, Archived
            $table->json('engagement_metrics')->nullable(); // JSON for likes, shares, comments, etc.
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaign_contents');
    }
};
