<?php

namespace App\Http\Controllers\Admin;

use App\DTOs\CreateMarketingCampaignDTO;
use App\Http\Controllers\Base\BaseController;
use App\Models\MarketingCampaign;
use App\Services\MarketingCampaignService;
use App\Services\Validation\Rules\MarketingCampaignRules;
use Illuminate\Http\Request;

class MarketingCampaignController extends BaseController
{
    public function __construct(MarketingCampaignService $marketingCampaignService)
    {
        parent::__construct(
            $marketingCampaignService,
            MarketingCampaignRules::class,
            'Admin/MarketingCampaigns',
            'marketingCampaigns',
            MarketingCampaign::class,
            CreateMarketingCampaignDTO::class
        );
    }

    public function printAll()
    {
        return app(MarketingCampaignService::class)->printAll(request());
    }

    // CSV export for marketing campaigns
    public function export(Request $request)
    {
        $campaigns = MarketingCampaign::query()
            ->with(['platform', 'assignedStaff', 'createdByStaff'])
            ->orderBy('campaign_name')
            ->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="marketing_campaigns.csv"',
        ];

        $columns = [
            'id', 'campaign_name', 'platform', 'start_date', 'end_date', 'status', 'urgentness', 'mandatoryness', 'responsible_staff', 'created_at',
        ];

        $callback = function () use ($campaigns, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            foreach ($campaigns as $campaign) {
                fputcsv($file, [
                    $campaign->id,
                    $campaign->campaign_name,
                    $campaign->platform ? $campaign->platform->name : '',
                    $campaign->start_date,
                    $campaign->end_date,
                    $campaign->status,
                    $campaign->urgentness ?? '',
                    $campaign->mandatoryness ?? '',
                    $campaign->assignedStaff ? $campaign->assignedStaff->first_name . ' ' . $campaign->assignedStaff->last_name : '',
                    $campaign->created_at,
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
