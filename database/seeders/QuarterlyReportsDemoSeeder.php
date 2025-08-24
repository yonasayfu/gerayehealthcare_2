<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MarketingCampaign;
use App\Models\CampaignMetric;
use App\Models\Invoice;
use App\Models\VisitService;
use Carbon\Carbon;

class QuarterlyReportsDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // --- Marketing ROI: Campaign Metrics across last ~12 months ---
        $campaigns = MarketingCampaign::query()->get();
        if ($campaigns->isEmpty()) {
            // Ensure there is at least some campaigns from MarketingRelatedSeeder
            $this->command?->warn('No marketing campaigns found. Creating 3 fallback campaigns via factory.');
            $campaigns = MarketingCampaign::factory()->count(3)->create();
        }

        $start = Carbon::now()->subMonths(12)->startOfMonth();
        $end = Carbon::now()->endOfMonth();

        foreach ($campaigns as $campaign) {
            // Weekly metrics to create nice aggregation per quarter
            $cursor = $start->copy();
            while ($cursor->lessThanOrEqualTo($end)) {
                CampaignMetric::factory()->create([
                    'campaign_id' => $campaign->id,
                    'date' => $cursor->toDateString(),
                ]);
                $cursor->addDays(7);
            }
        }

        // --- Revenue & AR: Invoices across last ~12 months ---
        // Mix of paid/unpaid with realistic dates
        Invoice::factory()->count(200)->create();

        // --- Service Volume: Visit Services across last ~12 months ---
        VisitService::factory()->count(400)->create();
    }
}
