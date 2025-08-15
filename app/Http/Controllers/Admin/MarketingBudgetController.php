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
use App\DTOs\CreateMarketingBudgetDTO;
use App\DTOs\UpdateMarketingBudgetDTO;

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
            CreateMarketingBudgetDTO::class,
            UpdateMarketingBudgetDTO::class
        );
    }

    public function create()
    {
        $campaigns = MarketingCampaign::select('id', 'campaign_name')->orderBy('campaign_name')->get();
        $platforms = MarketingPlatform::select('id', 'name')->orderBy('name')->get();

        return Inertia::render($this->viewName . '/Create', [
            'campaigns' => $campaigns,
            'platforms' => $platforms,
        ]);
    }

    public function edit($id)
    {
        $marketingBudget = $this->service->getById($id);
        $campaigns = MarketingCampaign::select('id', 'campaign_name')->orderBy('campaign_name')->get();
        $platforms = MarketingPlatform::select('id', 'name')->orderBy('name')->get();

        return Inertia::render($this->viewName . '/Edit', [
            lcfirst(class_basename($this->modelClass)) => $marketingBudget,
            'campaigns' => $campaigns,
            'platforms' => $platforms,
        ]);
    }
}
