<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Base\BaseController;
use App\Services\MarketingLeadService;
use App\Models\MarketingLead;
use App\Models\MarketingCampaign;
use App\Models\LandingPage;
use App\Models\Staff;
use App\Models\Patient;
use App\Services\Validation\Rules\MarketingLeadRules;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MarketingLeadController extends BaseController
{
    public function __construct(MarketingLeadService $marketingLeadService)
    {
        parent::__construct(
            $marketingLeadService,
            MarketingLeadRules::class,
            'Admin/MarketingLeads',
            'marketingLeads',
            MarketingLead::class,
            CreateMarketingLeadDTO::class
        );
    }

    

   
}