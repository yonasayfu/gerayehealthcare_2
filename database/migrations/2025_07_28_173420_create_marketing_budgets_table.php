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
        Schema::create('marketing_budgets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->nullable()->constrained('marketing_campaigns')->onDelete('cascade');
            $table->foreignId('platform_id')->nullable()->constrained('marketing_platforms')->onDelete('set null');
            $table->string('budget_name');
            $table->text('description')->nullable();
            $table->decimal('allocated_amount', 15, 2);
            $table->decimal('spent_amount', 15, 2)->default(0);
            $table->date('period_start');
            $table->date('period_end');
            $table->string('status')->default('Planned'); // Planned, Active, Completed, Overspent
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marketing_budgets');
    }
};
