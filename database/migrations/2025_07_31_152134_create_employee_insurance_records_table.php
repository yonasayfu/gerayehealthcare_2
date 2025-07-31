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
        Schema::create('employee_insurance_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->foreignId('policy_id')->constrained('insurance_policies')->onDelete('cascade');
            $table->string('kebele_id')->nullable();
            $table->string('woreda')->nullable();
            $table->string('region')->nullable();
            $table->string('federal_id')->nullable();
            $table->string('ministry_department')->nullable();
            $table->string('employee_id_number')->nullable();
            $table->boolean('verified')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_insurance_records');
    }
};
