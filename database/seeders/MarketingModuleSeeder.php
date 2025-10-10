<?php

namespace Database\Seeders;

use App\Models\CampaignContent;
use App\Models\CampaignMetric;
use App\Models\LeadSource;
use App\Models\MarketingBudget;
use App\Models\MarketingCampaign;
use App\Models\MarketingLead;
use App\Models\MarketingPlatform;
use App\Models\MarketingTask;
use App\Models\Staff;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class MarketingModuleSeeder extends Seeder
{
    public function run(): void
    {
        $leadSources = ['Website', 'Referral', 'TikTok', 'Facebook', 'Walk-in'];
        foreach ($leadSources as $source) {
            LeadSource::firstOrCreate(['name' => $source]);
        }

        $platforms = collect(['TikTok', 'Facebook', 'LinkedIn', 'Instagram', 'Website'])
            ->mapWithKeys(fn ($name) => [$name => MarketingPlatform::firstOrCreate(['name' => $name])]);

        $staffMembers = Staff::orderBy('id')->take(5)->get();
        if ($staffMembers->isEmpty()) {
            $staffMembers = Staff::factory()->count(5)->create();
        }

        $campaignDefinitions = [
            ['Home Nursing Awareness', 'Facebook'],
            ['Preventive Care Tips', 'Instagram'],
            ['Chronic Care Support', 'Website'],
            ['Caregiver Highlight Series', 'LinkedIn'],
            ['Urgent Care Express', 'TikTok'],
        ];

        $campaigns = collect();
        foreach ($campaignDefinitions as [$name, $platformName]) {
            $campaigns->push(
                MarketingCampaign::updateOrCreate(
                    ['campaign_name' => $name],
                    [
                        'platform_id' => optional($platforms[$platformName] ?? null)->id,
                        'start_date' => Carbon::now()->startOfMonth(),
                        'end_date' => Carbon::now()->addMonths(2)->endOfMonth(),
                        'status' => 'Active',
                    ]
                )
            );
        }

        foreach ($campaigns as $index => $campaign) {
            MarketingBudget::updateOrCreate(
                [
                    'campaign_id' => $campaign->id,
                    'budget_name' => $campaign->campaign_name.' '.Carbon::now()->format('Y-m'),
                ],
                [
                    'platform_id' => $campaign->platform_id,
                    'allocated_amount' => 2500 + ($index * 350),
                    'spent_amount' => 800 + ($index * 120),
                    'period_start' => Carbon::now()->startOfMonth(),
                    'period_end' => Carbon::now()->endOfMonth(),
                    'status' => 'Active',
                ]
            );

            $content = CampaignContent::updateOrCreate(
                [
                    'campaign_id' => $campaign->id,
                    'title' => $campaign->campaign_name.' Spotlight',
                ],
                [
                    'platform_id' => $campaign->platform_id,
                    'content_type' => Arr::random(['text', 'image', 'video']),
                    'description' => 'Automated seed content for '.$campaign->campaign_name,
                    'media_url' => 'https://example.com/media/'.Str::slug($campaign->campaign_name).'.jpg',
                    'scheduled_post_date' => Carbon::now()->addDays($index + 1),
                    'status' => 'scheduled',
                    'engagement_metrics' => [
                        'likes' => 120 + ($index * 15),
                        'comments' => 20 + ($index * 5),
                        'shares' => 10 + ($index * 3),
                    ],
                ]
            );

            CampaignMetric::updateOrCreate(
                [
                    'campaign_id' => $campaign->id,
                    'date' => Carbon::now()->subDays($index),
                ],
                [
                    'impressions' => 5000 + ($index * 800),
                    'clicks' => 320 + ($index * 40),
                    'conversions' => 60 + ($index * 8),
                    'cost_per_click' => 1.8 + ($index * 0.1),
                    'cost_per_conversion' => 48 + ($index * 2.5),
                    'roi_percentage' => 120 + ($index * 5),
                    'leads_generated' => 40 + ($index * 6),
                    'patients_acquired' => 18 + ($index * 3),
                    'revenue_generated' => 4200 + ($index * 500),
                    'engagement_rate' => 12 + ($index * 1.5),
                    'reach' => 4500 + ($index * 700),
                ]
            );

            MarketingLead::factory()->count(3)->create([
                'source_campaign_id' => $campaign->id,
                'status' => Arr::random(['New', 'Contacted', 'Converted']),
                'utm_source' => strtolower(optional($platforms[$platformName] ?? null)->name ?? 'direct'),
                'utm_campaign' => Str::slug($campaign->campaign_name),
                'utm_medium' => 'cpc',
            ]);

            $assignedStaff = $staffMembers[$index % $staffMembers->count()];
            MarketingTask::updateOrCreate(
                [
                    'campaign_id' => $campaign->id,
                    'title' => $campaign->campaign_name.' Follow-up Sequence',
                ],
                [
                    'assigned_to_staff_id' => $assignedStaff->id,
                    'related_content_id' => $content->id,
                    'doctor_id' => optional(Staff::whereHas('user', fn ($q) => $q->where('email', 'doctor@gerayehealthcare.com'))->first())->id,
                    'task_type' => 'Email Campaign',
                    'description' => 'Seeded marketing workflow item for '.$campaign->campaign_name,
                    'scheduled_at' => Carbon::now()->addDays($index + 2),
                    'status' => $index % 2 === 0 ? 'In Progress' : 'Pending',
                ]
            );
        }
    }
}
