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
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('chapa_tx_ref')->nullable()->unique();
            $table->string('chapa_checkout_url')->nullable();
            $table->string('chapa_payment_status')->nullable();
            $table->string('chapa_transaction_id')->nullable();
            $table->string('chapa_payment_method')->nullable();
            $table->timestamp('chapa_paid_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn([
                'chapa_tx_ref',
                'chapa_checkout_url',
                'chapa_payment_status',
                'chapa_transaction_id',
                'chapa_payment_method',
                'chapa_paid_at',
            ]);
        });
    }
};
