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
        Schema::create('insurance_policies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('insurance_company_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('corporate_client_id')->nullable()->constrained()->onDelete('set null');
            $table->string('service_type')->nullable();
            $table->string('service_type_amharic')->nullable();
            $table->decimal('coverage_percentage', 5, 2)->default(100.00);
            $table->string('coverage_type')->default('Full');
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('insurance_policies');
    }
};
