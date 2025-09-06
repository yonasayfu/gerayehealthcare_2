<?php

namespace App\Http\Controllers\Admin;

use App\DTOs\CreateLandingPageDTO;
use App\DTOs\UpdateLandingPageDTO;
use App\Http\Controllers\Base\BaseController;
use App\Models\LandingPage;
use App\Models\MarketingCampaign;
use App\Services\LandingPageService;
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
            LandingPage::class,
            CreateLandingPageDTO::class,
            UpdateLandingPageDTO::class
        );
    }

    public function create()
    {
        $campaigns = MarketingCampaign::select('id', 'campaign_name')->orderBy('campaign_name')->get();

        return Inertia::render($this->viewName.'/Create', [
            'campaigns' => $campaigns,
        ]);
    }

    public function printAll(Request $request)
    {
        return app(LandingPageService::class)->printAll($request);
    }

    public function printCurrent(Request $request)
    {
        return app(LandingPageService::class)->printCurrent($request);
    }

    public function edit($id)
    {
        $landingPage = $this->service->getById($id);
        $campaigns = MarketingCampaign::select('id', 'campaign_name')->orderBy('campaign_name')->get();

        return Inertia::render($this->viewName.'/Edit', [
            lcfirst(class_basename($this->modelClass)) => $landingPage,
            'campaigns' => $campaigns,
        ]);
    }
}
