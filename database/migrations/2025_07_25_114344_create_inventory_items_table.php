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
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('item_category')->nullable();
            $table->string('item_type')->nullable();
            $table->string('serial_number')->unique()->nullable();
            $table->date('purchase_date')->nullable();
            $table->date('warranty_expiry')->nullable();
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers');
            $table->string('assigned_to_type')->nullable();
            $table->unsignedBigInteger('assigned_to_id')->nullable();
            $table->date('last_maintenance_date')->nullable();
            $table->date('next_maintenance_due')->nullable();
            $table->string('maintenance_schedule')->nullable();
            $table->text('notes')->nullable();
            $table->string('status')->default('Available'); // e.g., Available, In Use, Under Maintenance, Lost, Damaged, Retired
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_items');
    }
};
