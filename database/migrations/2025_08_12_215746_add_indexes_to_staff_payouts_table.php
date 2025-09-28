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
        Schema::table('staff_payouts', function (Blueprint $table) {
            $table->index('staff_id', 'idx_staff_payouts_staff_id');
            $table->index('payout_date', 'idx_staff_payouts_payout_date');
            $table->index(['staff_id', 'payout_date'], 'idx_staff_payouts_staff_payout_date');
            $table->index('status', 'idx_staff_payouts_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('staff_payouts', function (Blueprint $table) {
            $table->dropIndex('idx_staff_payouts_staff_id');
            $table->dropIndex('idx_staff_payouts_payout_date');
            $table->dropIndex('idx_staff_payouts_staff_payout_date');
            $table->dropIndex('idx_staff_payouts_status');
        });
    }
};
