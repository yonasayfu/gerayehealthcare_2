<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Base\BaseController;
use App\Services\LandingPageService;
use App\Models\LandingPage;
use App\Models\MarketingCampaign;
use App\Services\Validation\Rules\LandingPageRules;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LandingPageController extends BaseController
{
    public function __construct(LandingPageService $landingPageService)
    {
        parent::__construct(
            $landingPageService,
            LandingPageRules::class,
            'Admin/LandingPages',
            'landingPages',
            LandingPage::class
        );
    }

    public function create()
    {
        return Inertia::render('Admin/LandingPages/Create', [
            'campaigns' => MarketingCampaign::all(['id', 'campaign_name']),
        ]);
    }

    public function show(LandingPage $landingPage)
    {
        return parent::show($landingPage->id);
    }

    public function edit(LandingPage $landingPage)
    {
        $data = $this->service->getById($landingPage->id);
        return Inertia::render('Admin/LandingPages/Edit', [
            'landingPage' => $data,
            'campaigns' => MarketingCampaign::all(['id', 'campaign_name']),
        ]);
    }

    public function update(Request $request, LandingPage $landingPage)
    {
        return parent::update($request, $landingPage->id);
    }

    public function destroy(LandingPage $landingPage)
    {
        return parent::destroy($landingPage->id);
    }

    public function export(Request $request, $type)
    {
        $request->merge(['type' => $type]);
        return parent::export($request);
    }
}