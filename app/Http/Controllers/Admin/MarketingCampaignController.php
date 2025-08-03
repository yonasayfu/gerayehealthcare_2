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
            MarketingCampaign::class
        );
    }

    public function create()
    {
        // You might want to pass platforms and staff to the create view
        return Inertia::render('Admin/MarketingCampaigns/Create', [
            'platforms' => MarketingPlatform::all(),
            'staff' => Staff::all(),
        ]);
    }

    public function show(MarketingCampaign $marketingCampaign)
    {
        return parent::show($marketingCampaign->id);
    }

    public function edit(MarketingCampaign $marketingCampaign)
    {
        $data = $this->service->getById($marketingCampaign->id);
        return Inertia::render('Admin/MarketingCampaigns/Edit', [
            'marketingCampaign' => $data,
            'platforms' => MarketingPlatform::all(),
            'staff' => Staff::all(),
        ]);
    }

    public function update(Request $request, MarketingCampaign $marketingCampaign)
    {
        return parent::update($request, $marketingCampaign->id);
    }

    public function destroy(MarketingCampaign $marketingCampaign)
    {
        return parent::destroy($marketingCampaign->id);
    }

    public function export(Request $request)
    {
        return parent::export($request);
    }

    public function printSingle(MarketingCampaign $marketingCampaign)
    {
        return parent::printSingle($marketingCampaign->id);
    }
}