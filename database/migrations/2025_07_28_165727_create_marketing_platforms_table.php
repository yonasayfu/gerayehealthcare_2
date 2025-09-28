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
        Schema::create('marketing_platforms', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // TikTok, Meta, Google, LinkedIn, etc.
            $table->string('api_endpoint')->nullable();
            // Consider encrypting api_credentials if storing sensitive data directly
            $table->text('api_credentials')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marketing_platforms');
    }
};
