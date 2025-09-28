<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medical_visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->timestamp('visit_date');
            $table->string('visit_type', 50); // initial, follow_up, emergency
            $table->foreignId('doctor_id')->constrained('staff');
            $table->string('status', 20)->default('scheduled'); // scheduled, completed, cancelled
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['patient_id', 'visit_date']);
            $table->index(['doctor_id', 'visit_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medical_visits');
    }
};
