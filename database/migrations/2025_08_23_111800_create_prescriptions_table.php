<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('prescriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->foreignId('medical_document_id')->nullable()->constrained('medical_documents')->nullOnDelete();
            $table->date('prescribed_date');
            $table->string('status', 20)->default('active'); // active, completed, cancelled
            $table->text('instructions')->nullable();
            $table->foreignId('created_by_staff_id')->constrained('staff');
            $table->timestamps();

            $table->index(['patient_id', 'prescribed_date']);
            $table->index(['status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prescriptions');
    }
};
