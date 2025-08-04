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
            LandingPage::class,
            CreateLandingPageDTO::class
        );
    }

    
}