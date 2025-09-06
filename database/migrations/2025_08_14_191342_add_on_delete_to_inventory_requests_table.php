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
        Schema::table('inventory_requests', function (Blueprint $table) {
            $table->dropForeign(['item_id']);
            $table->foreign('item_id')
                ->references('id')
                ->on('inventory_items')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory_requests', function (Blueprint $table) {
            $table->dropForeign(['item_id']);
            $table->foreign('item_id')
                ->references('id')
                ->on('inventory_items');
        });
    }
};
