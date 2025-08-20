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
        Schema::create('shared_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->onDelete('cascade');
            $table->foreignId('partner_id')->constrained('partners')->onDelete('cascade');
            $table->foreignId('shared_by_staff_id')->nullable()->constrained('staff')->onDelete('set null');
            $table->date('share_date');
            $table->string('status', 50)->default('Shared')->comment("'Shared', 'Acknowledged', 'Action-Required'");
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::table('shared_invoices', function (Blueprint $table) {
            DB::statement("COMMENT ON TABLE shared_invoices IS 'Tracks invoices that are explicitly shared with partners for commission or collaboration purposes.'");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shared_invoices');
    }
};
