<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            // SQLite does not fully support complex views with PostgreSQL-specific functions like DATE_TRUNC and TO_CHAR.
            // We will skip creating this view for SQLite environments, typically used for testing.
            return;
        }

        // Base index to support date filtering (noop if already exists in another migration)
        DB::statement('CREATE INDEX IF NOT EXISTS idx_visit_services_scheduled_at ON visit_services (scheduled_at);');

        DB::statement(<<<'SQL'
        CREATE OR REPLACE VIEW service_volume_view AS
        SELECT
            DATE_TRUNC('quarter', vs.scheduled_at)::date AS bucket_date,
            TO_CHAR(DATE_TRUNC('quarter', vs.scheduled_at), 'YYYY-"Q"Q') AS bucket_label,
            'quarter'::text AS granularity,
            s.category AS service_category,
            COUNT(*) AS total_visits,
            COUNT(DISTINCT vs.patient_id) AS unique_patients,
            (vs.event_id IS NOT NULL) AS is_event_service
        FROM visit_services vs
        LEFT JOIN services s ON s.id = vs.service_id
        WHERE vs.scheduled_at IS NOT NULL
        GROUP BY 1,2,4,7;
        SQL);
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            return;
        }
        DB::statement('DROP VIEW IF EXISTS service_volume_view;');
    }
};
