<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medical_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->foreignId('medical_visit_id')->nullable()->constrained('medical_visits')->nullOnDelete();
            $table->string('document_type', 50); // doctor_note, lab_request, lab_result, prescription
            $table->string('title', 255);
            $table->date('document_date');
            $table->string('file_path', 512)->nullable(); // scanned file path
            $table->text('summary')->nullable();
            $table->boolean('is_printed')->default(false);
            $table->foreignId('created_by_staff_id')->constrained('staff');
            $table->timestamps();

            $table->index(['patient_id', 'document_date']);
            $table->index(['document_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medical_documents');
    }
};
