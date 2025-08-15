<?php

namespace App\Http\Controllers\Admin;

use App\DTOs\CreateMarketingLeadDTO;
use App\DTOs\UpdateMarketingLeadDTO;
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
            CreateMarketingLeadDTO::class,
            UpdateMarketingLeadDTO::class
        );
    }

    public function create()
    {
        $campaigns = MarketingCampaign::select('id', 'campaign_name')->orderBy('campaign_name')->get();
        $landingPages = LandingPage::select('id', 'page_name')->orderBy('page_name')->get();
        $staff = Staff::select('id', 'first_name', 'last_name')->orderBy('first_name')->get();
        $patients = Patient::select('id', 'full_name')->orderBy('full_name')->get();

        return Inertia::render($this->viewName . '/Create', [
            'campaigns' => $campaigns,
            'landingPages' => $landingPages,
            'staff' => $staff,
            'patients' => $patients,
        ]);
    }

    public function edit($id)
    {
        $marketingLead = $this->service->getById($id);
        $campaigns = MarketingCampaign::select('id', 'campaign_name')->orderBy('campaign_name')->get();
        $landingPages = LandingPage::select('id', 'page_name')->orderBy('page_name')->get();
        $staff = Staff::select('id', 'first_name', 'last_name')->orderBy('first_name')->get();
        $patients = Patient::select('id', 'full_name')->orderBy('full_name')->get();

        return Inertia::render($this->viewName . '/Edit', [
            lcfirst(class_basename($this->modelClass)) => $marketingLead,
            'campaigns' => $campaigns,
            'landingPages' => $landingPages,
            'staff' => $staff,
            'patients' => $patients,
        ]);
    }
}
