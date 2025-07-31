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
        Schema::create('insurance_claims', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->foreignId('invoice_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('insurance_company_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('policy_id')->nullable()->constrained('insurance_policies')->onDelete('set null');
            $table->string('claim_status')->default('Submitted');
            $table->decimal('coverage_amount', 10, 2)->nullable();
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->date('payment_due_date')->nullable();
            $table->timestamp('payment_received_at')->nullable();
            $table->string('payment_method')->nullable();
            $table->boolean('reimbursement_required')->default(false);
            $table->string('receipt_number')->nullable();
            $table->boolean('is_pre_authorized')->default(false);
            $table->string('pre_authorization_code')->nullable();
            $table->text('denial_reason')->nullable();
            $table->text('translated_notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('insurance_claims');
    }
};
