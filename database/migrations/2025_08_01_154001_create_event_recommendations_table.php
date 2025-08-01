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
        Schema::create('event_recommendations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->string('source_channel'); // e.g., social_media, form, community_outreach
            $table->string('recommended_by_name')->nullable();
            $table->string('recommended_by_phone')->nullable();
            $table->string('patient_name');
            $table->integer('age')->nullable();
            $table->string('gender')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('region')->nullable();
            $table->string('woreda')->nullable();
            $table->text('reason')->nullable();
            $table->foreignId('linked_patient_id')->nullable()->constrained('patients')->onDelete('set null');
            $table->text('notes')->nullable();
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_recommendations');
    }
};
