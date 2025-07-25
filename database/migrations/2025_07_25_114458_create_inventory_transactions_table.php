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
        Schema::create('inventory_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('inventory_items');
            $table->foreignId('request_id')->nullable()->constrained('inventory_requests');
            $table->string('from_location')->nullable();
            $table->string('to_location')->nullable();
            $table->string('from_assigned_to_type')->nullable();
            $table->unsignedBigInteger('from_assigned_to_id')->nullable();
            $table->string('to_assigned_to_type')->nullable();
            $table->unsignedBigInteger('to_assigned_to_id')->nullable();
            $table->integer('quantity');
            $table->string('transaction_type');
            $table->foreignId('performed_by_id')->constrained('staff');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_transactions');
    }
};
