<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventory_requests', function (Blueprint $table) {
            // Drop existing foreign keys first
            $table->dropForeign(['requester_id']);
            $table->dropForeign(['approver_id']);
            $table->dropForeign(['item_id']);

            // Re-add foreign keys with onDelete actions
            $table->foreign('requester_id')->references('id')->on('staff')->onDelete('set null');
            $table->foreign('approver_id')->references('id')->on('staff')->onDelete('set null');
            $table->foreign('item_id')->references('id')->on('inventory_items')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('inventory_requests', function (Blueprint $table) {
            // Reverse the changes in down method
            $table->dropForeign(['requester_id']);
            $table->dropForeign(['approver_id']);
            $table->dropForeign(['item_id']);

            // Re-add without onDelete actions (or with default RESTRICT)
            $table->foreign('requester_id')->references('id')->on('staff');
            $table->foreign('approver_id')->references('id')->on('staff');
            $table->foreign('item_id')->references('id')->on('inventory_items');
        });
    }
};
