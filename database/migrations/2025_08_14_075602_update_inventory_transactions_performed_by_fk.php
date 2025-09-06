<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventory_transactions', function (Blueprint $table) {
            // Drop the foreign key and column
            $table->dropConstrainedForeignId('performed_by_id');
        });

        Schema::table('inventory_transactions', function (Blueprint $table) {
            // Re-add the column as nullable with ON DELETE SET NULL
            $table->foreignId('performed_by_id')
                ->nullable()
                ->constrained('staff')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('inventory_transactions', function (Blueprint $table) {
            // Drop the foreign key and column
            $table->dropConstrainedForeignId('performed_by_id');
        });

        Schema::table('inventory_transactions', function (Blueprint $table) {
            // Re-add the column as non-nullable with the original foreign key constraint
            $table->foreignId('performed_by_id')
                ->constrained('staff');
        });
    }
};
