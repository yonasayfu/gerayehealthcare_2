<?php

namespace Database\Seeders;

use App\Models\CampaignMetric;
use App\Models\LeadSource;
use App\Models\MarketingBudget;
use App\Models\MarketingCampaign;
use App\Models\MarketingLead;
use App\Models\MarketingPlatform;
use App\Models\LandingPage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class MarketingDemoSeeder extends Seeder
{
    public function run(): void
    {
        LandingPage::query()->delete();
        // Lead sources
        $sources = ['Website', 'Referral', 'TikTok', 'Facebook', 'Walk-in'];
        foreach ($sources as $name) {
            LeadSource::firstOrCreate(['name' => $name]);
        }

        // Platforms
        $platformMap = [];
        foreach (['TikTok', 'Facebook', 'Website'] as $pname) {
            $platformMap[$pname] = MarketingPlatform::firstOrCreate(['name' => $pname])->id;
        }

        // Campaigns with budgets
        $now = Carbon::now();
        $start = $now->copy()->startOfMonth();
        $end = $now->copy()->endOfMonth();

        $campaigns = [];
        foreach ([
            ['Home Nursing Awareness', 'Facebook'],
            ['Physio at Home', 'TikTok'],
            ['Chronic Care Support', 'Website'],
        ] as [$name, $platformName]) {
            $c = MarketingCampaign::firstOrCreate([
                'campaign_name' => $name,
            ], [
                'platform_id' => $platformMap[$platformName] ?? null,
                'start_date' => $start,
                'end_date' => $end,
                'status' => 'Active',
            ]);
            $campaigns[] = $c;
            MarketingBudget::create([
                'campaign_id' => $c->id,
                'platform_id' => null,
                'budget_name' => $name.' '.$start->format('Y-m'),
                'allocated_amount' => rand(1000, 5000),
                'spent_amount' => rand(0, 500),
                'period_start' => $start,
                'period_end' => $end,
                'status' => 'Active',
            ]);
        }

        // Campaign metrics over the month
        foreach ($campaigns as $c) {
            for ($i = 0; $i < 10; $i++) {
                $impr = rand(1000, 15000);
                $clicks = rand(50, 800);
                $conversions = rand(5, 120);
                $revenue = rand(500, 5000);
                $cpc = $clicks ? round(rand(10, 150) / 10, 2) : null; // $1.0–$15.0
                $cpa = $conversions ? round(rand(50, 300) + rand(0, 99) / 100, 2) : null; // $50–$300
                $leads = max($conversions, rand(10, 150));
                $patients = max(0, (int) floor($conversions * 0.6));

                CampaignMetric::create([
                    'campaign_id' => $c->id,
                    'date' => $start->copy()->addDays(rand(0, $end->day - 1)),
                    'impressions' => $impr,
                    'clicks' => $clicks,
                    'conversions' => $conversions,
                    'cost_per_click' => $cpc,
                    'cost_per_conversion' => $cpa,
                    'roi_percentage' => round(rand(50, 250) + rand(0, 99) / 100, 2),
                    'leads_generated' => $leads,
                    'patients_acquired' => $patients,
                    'revenue_generated' => $revenue,
                    'engagement_rate' => round(rand(10, 500) / 10, 2), // 1.0–50.0%
                    'reach' => $impr - rand(0, (int) ($impr * 0.2)),
                ]);
            }
        }

        // Marketing leads
        foreach (range(1, 40) as $i) {
            $campaign = $campaigns[array_rand($campaigns)];
            $platformName = MarketingPlatform::find($campaign->platform_id)?->name ?? 'Website';
            MarketingLead::factory()->create([
                'source_campaign_id' => $campaign->id,
                'status' => rand(0, 1) ? 'Converted' : 'New',
                'utm_source' => strtolower($platformName),
                'utm_campaign' => $campaign->campaign_name,
                'utm_medium' => 'cpc',
            ]);
        }
    }
}
