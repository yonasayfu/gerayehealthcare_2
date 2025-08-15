<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventory_transactions', function (Blueprint $table) {
            // Drop existing FK that blocks staff deletion
            $table->dropForeign('inventory_transactions_performed_by_id_foreign');
        });

        // Make the column nullable (PostgreSQL compatible without doctrine/dbal)
        DB::statement('ALTER TABLE inventory_transactions ALTER COLUMN performed_by_id DROP NOT NULL');

        Schema::table('inventory_transactions', function (Blueprint $table) {
            // Recreate FK with ON DELETE SET NULL
            $table->foreign('performed_by_id')
                ->references('id')
                ->on('staff')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('inventory_transactions', function (Blueprint $table) {
            $table->dropForeign(['performed_by_id']);
        });

        // Revert column to NOT NULL (if it had data, ensure no NULLs before running down)
        DB::statement('UPDATE inventory_transactions SET performed_by_id = 0 WHERE performed_by_id IS NULL');
        DB::statement('ALTER TABLE inventory_transactions ALTER COLUMN performed_by_id SET NOT NULL');

        Schema::table('inventory_transactions', function (Blueprint $table) {
            // Recreate the original FK without ON DELETE rule (default RESTRICT/NO ACTION)
            $table->foreign('performed_by_id')
                ->references('id')
                ->on('staff');
        });
    }
};
