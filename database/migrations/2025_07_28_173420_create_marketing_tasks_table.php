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
        Schema::create('marketing_tasks', function (Blueprint $table) {
            $table->id();
            $table->string('task_code')->unique(); // Auto-generated like TASK-00001
            $table->foreignId('campaign_id')->nullable()->constrained('marketing_campaigns')->onDelete('set null');
            $table->foreignId('assigned_to_staff_id')->nullable()->constrained('staff')->onDelete('cascade');
            $table->foreignId('related_content_id')->nullable()->constrained('campaign_contents')->onDelete('set null');
            $table->foreignId('doctor_id')->nullable()->constrained('staff')->onDelete('cascade'); // Assuming doctors are also staff
            $table->string('task_type'); // e.g., Content Creation, Posting, Doctor Filming, Lead Follow-up
            $table->string('title');
            $table->text('description')->nullable();
            $table->timestamp('scheduled_at');
            $table->timestamp('completed_at')->nullable();
            $table->string('status')->default('Pending'); // Pending, In Progress, Completed, Cancelled
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marketing_tasks');
    }
};
