<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Only run these statements if not using SQLite, as SQLite handles FKs differently
        if (DB::connection()->getDriverName() !== 'sqlite') {
            // Force-drop and recreate FK for PostgreSQL to ensure ON DELETE SET NULL
            DB::statement('ALTER TABLE inventory_transactions DROP CONSTRAINT IF EXISTS inventory_transactions_performed_by_id_foreign');
            DB::statement('ALTER TABLE inventory_transactions ALTER COLUMN performed_by_id DROP NOT NULL');
            DB::statement('ALTER TABLE inventory_transactions ADD CONSTRAINT inventory_transactions_performed_by_id_foreign FOREIGN KEY (performed_by_id) REFERENCES staff(id) ON DELETE SET NULL');
        }
    }

    public function down(): void
    {
        // Only run these statements if not using SQLite
        if (DB::connection()->getDriverName() !== 'sqlite') {
            // Revert to restrictive FK (NO ACTION) and NOT NULL (if desired)
