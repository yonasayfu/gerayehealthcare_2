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
        Schema::create('referral_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('referral_id')->constrained('referrals')->onDelete('cascade');
            $table->foreignId('uploaded_by_staff_id')->nullable()->constrained('staff')->onDelete('set null');
            $table->string('document_name');
            $table->string('document_path');
            $table->string('document_type', 100)->nullable()->comment("'Clinical Summary', 'Prescription', 'Lab Result', 'Imaging Report'");
            $table->string('status', 50)->default('Uploaded')->comment("'Uploaded', 'Sent', 'Received', 'Reviewed'");
            $table->timestamps();
        });

        Schema::table('referral_documents', function (Blueprint $table) {
            DB::statement("COMMENT ON TABLE referral_documents IS 'Manages medical and administrative documents associated with a partner referral.'");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referral_documents');
    }
};
