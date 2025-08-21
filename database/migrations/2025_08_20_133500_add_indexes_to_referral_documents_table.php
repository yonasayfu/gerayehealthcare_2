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
        Schema::table('referral_documents', function (Blueprint $table) {
            // Individual indexes commonly used for filtering/searching
            $table->index('referral_id');
            $table->index('uploaded_by_staff_id');
            $table->index('status');
            $table->index('document_type');
            $table->index('created_at');

            // Helpful composite index for frequent filters
            $table->index(['referral_id', 'status'], 'referral_documents_referral_status_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('referral_documents', function (Blueprint $table) {
            $table->dropIndex(['referral_id']);
            $table->dropIndex(['uploaded_by_staff_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['document_type']);
            $table->dropIndex(['created_at']);
            $table->dropIndex('referral_documents_referral_status_idx');
        });
    }
};
