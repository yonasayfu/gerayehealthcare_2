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
    Schema::create('caregiver_assignments', function (Blueprint $table) {
        $table->id();
        $table->foreignId('staff_id')->constrained()->onDelete('cascade');
        $table->foreignId('patient_id')->constrained()->onDelete('cascade');
        $table->timestamp('shift_start')->nullable();
        $table->timestamp('shift_end')->nullable();
        $table->string('status')->default('Assigned');
        $table->timestamps();
    });
}ph

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('caregiver_assignments');
    }
};
