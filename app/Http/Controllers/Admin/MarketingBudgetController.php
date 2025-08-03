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
            MarketingBudget::class
        );
    }

    public function create()
    {
        $campaigns = MarketingCampaign::all();
        $platforms = MarketingPlatform::all();
        $statuses = ['Planned', 'Active', 'Completed', 'On Hold', 'Cancelled'];

        return Inertia::render('Admin/MarketingBudgets/Create', [
            'campaigns' => $campaigns,
            'platforms' => $platforms,
            'statuses' => $statuses,
        ]);
    }

    public function show(MarketingBudget $marketingBudget)
    {
        return parent::show($marketingBudget->id);
    }

    public function edit(MarketingBudget $marketingBudget)
    {
        $data = $this->service->getById($marketingBudget->id);
        $campaigns = MarketingCampaign::all();
        $platforms = MarketingPlatform::all();
        $statuses = ['Planned', 'Active', 'Completed', 'On Hold', 'Cancelled'];

        return Inertia::render('Admin/MarketingBudgets/Edit', [
            'marketingBudget' => $data,
            'campaigns' => $campaigns,
            'platforms' => $platforms,
            'statuses' => $statuses,
        ]);
    }

    public function update(Request $request, MarketingBudget $marketingBudget)
    {
        return parent::update($request, $marketingBudget->id);
    }

    public function destroy(MarketingBudget $marketingBudget)
    {
        return parent::destroy($marketingBudget->id);
    }

    public function export(Request $request)
    {
        return parent::export($request);
    }

    public function printAll(Request $request)
    {
        return parent::printAll($request);
    }

    public function printCurrent(Request $request)
    {
        return parent::printCurrent($request);
    }
}