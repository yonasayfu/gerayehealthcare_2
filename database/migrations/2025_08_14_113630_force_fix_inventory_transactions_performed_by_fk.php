<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Force-drop and recreate FK for PostgreSQL to ensure ON DELETE SET NULL
        DB::statement('ALTER TABLE inventory_transactions DROP CONSTRAINT IF EXISTS inventory_transactions_performed_by_id_foreign');
        DB::statement('ALTER TABLE inventory_transactions ALTER COLUMN performed_by_id DROP NOT NULL');
        DB::statement('ALTER TABLE inventory_transactions ADD CONSTRAINT inventory_transactions_performed_by_id_foreign FOREIGN KEY (performed_by_id) REFERENCES staff(id) ON DELETE SET NULL');
    }

    public function down(): void
    {
        // Revert to restrictive FK (NO ACTION) and NOT NULL (if desired)
        DB::statement('ALTER TABLE inventory_transactions DROP CONSTRAINT IF EXISTS inventory_transactions_performed_by_id_foreign');
        // Note: We cannot safely enforce NOT NULL if NULLs exist; skip altering nullability in down.
        DB::statement('ALTER TABLE inventory_transactions ADD CONSTRAINT inventory_transactions_performed_by_id_foreign FOREIGN KEY (performed_by_id) REFERENCES staff(id)');
    }
};
