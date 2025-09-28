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
        Schema::create('partners', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type', 100);
            $table->string('contact_person')->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('engagement_status', 50)->default('Prospect');
            $table->foreignId('account_manager_id')->nullable()->constrained('staff')->onDelete('set null');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('partner_agreements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('partner_id')->constrained('partners')->onDelete('cascade');
            $table->string('agreement_title');
            $table->string('agreement_type', 100);
            $table->string('status', 50)->default('Draft');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->string('priority_service_level', 50)->nullable();
            $table->string('commission_type', 50)->nullable();
            $table->decimal('commission_rate', 8, 2)->nullable();
            $table->string('terms_document_path')->nullable();
            $table->foreignId('signed_by_staff_id')->nullable()->constrained('staff')->onDelete('set null');
            $table->timestamps();
        });

        Schema::create('referrals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('partner_id')->constrained('partners')->onDelete('cascade');
            $table->foreignId('agreement_id')->nullable()->constrained('partner_agreements')->onDelete('set null');
            $table->foreignId('referred_patient_id')->unique()->constrained('patients')->onDelete('cascade');
            $table->date('referral_date');
            $table->string('status', 50)->default('Pending');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('partner_commissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agreement_id')->constrained('partner_agreements')->onDelete('cascade');
            $table->foreignId('referral_id')->constrained('referrals')->onDelete('cascade');
            $table->foreignId('invoice_id')->constrained('invoices')->onDelete('cascade');
            $table->decimal('commission_amount', 10, 2);
            $table->date('calculation_date');
            $table->date('payout_date')->nullable();
            $table->string('status', 50)->default('Due');
            $table->timestamps();
        });

        Schema::create('partner_engagements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('partner_id')->constrained('partners')->onDelete('cascade');
            $table->foreignId('staff_id')->nullable()->constrained('staff')->onDelete('set null');
            $table->string('engagement_type', 100);
            $table->text('summary');
            $table->timestamp('engagement_date');
            $table->date('follow_up_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partner_engagements');
        Schema::dropIfExists('partner_commissions');
        Schema::dropIfExists('referrals');
        Schema::dropIfExists('partner_agreements');
        Schema::dropIfExists('partners');
    }
};
