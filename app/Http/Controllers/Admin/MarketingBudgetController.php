<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Base\BaseController;
use App\Models\MarketingBudget;
use App\Models\MarketingBudget as MarketingBudgetModel;
use App\Models\MarketingCampaign;
use App\Models\MarketingPlatform;
use App\Services\MarketingBudget\MarketingBudgetService;
use App\Services\Validation\Rules\MarketingBudgetRules;
use Illuminate\Http\Request;
// Using direct validated arrays for create/update (no DTO)
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
            MarketingBudget::class
        );
    }

    public function create()
    {
        $campaigns = MarketingCampaign::select('id', 'campaign_name')->orderBy('campaign_name')->get();
        $platforms = MarketingPlatform::select('id', 'name')->orderBy('name')->get();
        $statuses = ['Planned', 'Active', 'Completed', 'On Hold', 'Cancelled'];

        return Inertia::render($this->viewName.'/Create', [
            'campaigns' => $campaigns,
            'platforms' => $platforms,
            'statuses' => $statuses,
        ]);
    }

    public function edit($id)
    {
        $marketingBudget = $this->service->getById($id);
        $campaigns = MarketingCampaign::select('id', 'campaign_name')->orderBy('campaign_name')->get();
        $platforms = MarketingPlatform::select('id', 'name')->orderBy('name')->get();
        $statuses = ['Planned', 'Active', 'Completed', 'On Hold', 'Cancelled'];

        return Inertia::render($this->viewName.'/Edit', [
            lcfirst(class_basename($this->modelClass)) => $marketingBudget,
            'campaigns' => $campaigns,
            'platforms' => $platforms,
            'statuses' => $statuses,
        ]);
    }

    public function printAll(Request $request)
    {
        return $this->service->printAll($request);
    }

    public function printCurrent(Request $request)
    {
        return $this->service->printCurrent($request);
    }

    public function printSingle(Request $request, MarketingBudgetModel $marketing_budget)
    {
        return $this->service->printSingle($request, $marketing_budget);
    }

    public function export(Request $request)
    {
        return $this->service->export($request);
    }
}
