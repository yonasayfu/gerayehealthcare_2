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

        // Index to support date filtering on campaign metrics
        DB::statement('CREATE INDEX IF NOT EXISTS idx_campaign_metrics_date ON campaign_metrics (date);');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_campaign_metrics_campaign_id ON campaign_metrics (campaign_id);');

        DB::statement(<<<'SQL'
        CREATE OR REPLACE VIEW marketing_roi_view AS
        SELECT
            DATE_TRUNC('quarter', cm.date)::date AS bucket_date,
            TO_CHAR(DATE_TRUNC('quarter', cm.date), 'YYYY-"Q"Q') AS bucket_label,
            'quarter'::text AS granularity,
            mp.name AS platform,
            SUM(cm.impressions) AS impressions,
            SUM(cm.clicks) AS clicks,
            SUM(cm.conversions) AS conversions,
            SUM(COALESCE(cm.revenue_generated, 0))::numeric(15,2) AS revenue_generated,
            -- Approximate spend: CPC*clicks + CPA*conversions when available
            (SUM(COALESCE(cm.cost_per_click, 0) * COALESCE(cm.clicks, 0))
             + SUM(COALESCE(cm.cost_per_conversion, 0) * COALESCE(cm.conversions, 0)))::numeric(15,2) AS spend,
            CASE
                WHEN (SUM(COALESCE(cm.cost_per_click, 0) * COALESCE(cm.clicks, 0)) +
                      SUM(COALESCE(cm.cost_per_conversion, 0) * COALESCE(cm.conversions, 0))) > 0
                THEN ROUND(((SUM(COALESCE(cm.revenue_generated, 0)) -
                             (SUM(COALESCE(cm.cost_per_click, 0) * COALESCE(cm.clicks, 0)) +
                              SUM(COALESCE(cm.cost_per_conversion, 0) * COALESCE(cm.conversions, 0))))
                           /
                           (SUM(COALESCE(cm.cost_per_click, 0) * COALESCE(cm.clicks, 0)) +
                            SUM(COALESCE(cm.cost_per_conversion, 0) * COALESCE(cm.conversions, 0))) * 100.0), 2)
                ELSE 0.00
            END::numeric(8,2) AS roi_percentage
        FROM campaign_metrics cm
        JOIN marketing_campaigns mc ON mc.id = cm.campaign_id
        JOIN marketing_platforms mp ON mp.id = mc.platform_id
        GROUP BY 1,2,4;
        SQL);
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            return;
        }
        DB::statement('DROP VIEW IF EXISTS marketing_roi_view;');
    }
};
