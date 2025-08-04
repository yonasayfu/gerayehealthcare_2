<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Base\BaseController;
use App\Services\MarketingCampaignService;
use App\Models\MarketingCampaign;
use App\Models\MarketingPlatform;
use App\Models\Staff;
use App\Services\Validation\Rules\MarketingCampaignRules;
use Illuminate\Http\Request;
use Inertia\Inertia;

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

    

   
}