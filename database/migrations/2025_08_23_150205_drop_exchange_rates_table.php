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
        Schema::dropIfExists('exchange_rates');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate the table structure as it was previously
        Schema::create('exchange_rates', function (Blueprint $table) {
            $table->id();
            $table->string('currency_code', 10);
            $table->decimal('rate_to_etb', 10, 4);
            $table->string('source')->nullable();
            $table->date('date_effective');
            $table->timestamps();
        });
    }
};
