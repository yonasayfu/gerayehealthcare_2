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
            MarketingLead::class
        );
    }

    public function create()
    {
        return Inertia::render('Admin/MarketingLeads/Create', [
            'campaigns' => MarketingCampaign::all(),
            'landingPages' => LandingPage::all(),
            'staffMembers' => Staff::all(),
            'patients' => Patient::all(),
            'statuses' => ['New', 'Contacted', 'Qualified', 'Disqualified', 'Converted'],
        ]);
    }

    public function show(MarketingLead $marketingLead)
    {
        return parent::show($marketingLead->id);
    }

    public function edit(MarketingLead $marketingLead)
    {
        $data = $this->service->getById($marketingLead->id);
        return Inertia::render('Admin/MarketingLeads/Edit', [
            'marketingLead' => $data,
            'campaigns' => MarketingCampaign::all(),
            'landingPages' => LandingPage::all(),
            'staffMembers' => Staff::all(),
            'patients' => Patient::all(),
            'statuses' => ['New', 'Contacted', 'Qualified', 'Disqualified', 'Converted'],
        ]);
    }

    public function update(Request $request, MarketingLead $marketingLead)
    {
        return parent::update($request, $marketingLead->id);
    }

    public function destroy(MarketingLead $marketingLead)
    {
        return parent::destroy($marketingLead->id);
    }

    public function export(Request $request)
    {
        return parent::export($request);
    }

    public function printSingle(MarketingLead $marketingLead)
    {
        return parent::printSingle($marketingLead->id);
    }
}