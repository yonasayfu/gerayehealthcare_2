<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payout_visit_service', function (Blueprint $table) {
            $table->foreignId('staff_payout_id')->constrained('staff_payouts')->onDelete('cascade');
            $table->foreignId('visit_service_id')->constrained('visit_services')->onDelete('cascade');
            $table->primary(['staff_payout_id', 'visit_service_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payout_visit_service');
    }
};