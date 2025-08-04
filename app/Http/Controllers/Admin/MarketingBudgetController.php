<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Base\BaseController;
use App\Services\MarketingBudgetService;
use App\Models\MarketingBudget;
use App\Models\MarketingCampaign;
use App\Models\MarketingPlatform;
use App\Services\Validation\Rules\MarketingBudgetRules;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MarketingBudgetController extends BaseController
{
    public function __construct(MarketingBudgetService $marketingBudgetService)
    {
        parent::__construct(
            $marketingBudgetService,
            MarketingBudgetRules::class,
            'Admin/MarketingBudgets',
            'marketingBudgets',
            MarketingBudget::class,
            CreateMarketingBudgetDTO::class
        );
    }

    

    
}