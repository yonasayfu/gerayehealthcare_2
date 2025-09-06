<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Indexes to support filtering by invoice_date and status
        DB::statement('CREATE INDEX IF NOT EXISTS idx_invoices_invoice_date ON invoices (invoice_date);');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_invoices_status ON invoices (status);');

        DB::statement(<<<'SQL'
        CREATE OR REPLACE VIEW revenue_ar_view AS
        SELECT
            DATE_TRUNC('quarter', i.invoice_date)::date AS bucket_date,
            TO_CHAR(DATE_TRUNC('quarter', i.invoice_date), 'YYYY-"Q"Q') AS bucket_label,
            'quarter'::text AS granularity,
            COUNT(*) AS invoices_count,
            SUM(i.grand_total)::numeric(15,2) AS total_billed,
            SUM(COALESCE(i.amount, 0))::numeric(15,2) AS total_received,
            (SUM(i.grand_total) - SUM(COALESCE(i.amount, 0)))::numeric(15,2) AS ar_outstanding,
            SUM(CASE WHEN i.status = 'Paid' OR i.paid_at IS NOT NULL THEN 1 ELSE 0 END) AS paid_invoices,
            SUM(CASE WHEN i.status <> 'Paid' AND (i.paid_at IS NULL) THEN 1 ELSE 0 END) AS unpaid_invoices
        FROM invoices i
        GROUP BY 1,2;
        SQL);
    }

    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS revenue_ar_view;');
    }
};
