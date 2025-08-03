<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Base\BaseController;
use App\Services\MarketingTaskService;
use App\Models\MarketingTask;
use App\Models\MarketingCampaign;
use App\Models\Staff;
use App\Models\CampaignContent;
use App\Services\Validation\Rules\MarketingTaskRules;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class MarketingTaskController extends BaseController
{
    use AuthorizesRequests;

    public function __construct(MarketingTaskService $marketingTaskService)
    {
        parent::__construct(
            $marketingTaskService,
            MarketingTaskRules::class,
            'Admin/MarketingTasks',
            'marketingTasks',
            MarketingTask::class
        );
    }

    public function create()
    {
        $this->authorize('create', MarketingTask::class);
        return Inertia::render('Admin/MarketingTasks/Create', [
            'campaigns' => MarketingCampaign::all(),
            'staffs' => Staff::with('user')->get(),
            'campaignContents' => CampaignContent::all(),
            'taskTypes' => ['Email Campaign', 'Social Media Post', 'Ad Creation', 'Content Writing', 'SEO Optimization'],
            'statuses' => ['Pending', 'In Progress', 'Completed', 'Cancelled'],
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', MarketingTask::class);
        return parent::store($request);
    }

    public function show(MarketingTask $marketingTask)
    {
        $this->authorize('view', $marketingTask);
        return parent::show($marketingTask->id);
    }

    public function edit(MarketingTask $marketingTask)
    {
        $this->authorize('update', $marketingTask);
        $data = $this->service->getById($marketingTask->id);
        return Inertia::render('Admin/MarketingTasks/Edit', [
            'marketingTask' => $data,
            'campaigns' => MarketingCampaign::all(),
            'staffs' => Staff::with('user')->get(),
            'campaignContents' => CampaignContent::all(),
            'taskTypes' => ['Email Campaign', 'Social Media Post', 'Ad Creation', 'Content Writing', 'SEO Optimization'],
            'statuses' => ['Pending', 'In Progress', 'Completed', 'Cancelled'],
        ]);
    }

    public function update(Request $request, MarketingTask $marketingTask)
    {
        $this->authorize('update', $marketingTask);
        return parent::update($request, $marketingTask->id);
    }

    public function destroy(MarketingTask $marketingTask)
    {
        $this->authorize('delete', $marketingTask);
        return parent::destroy($marketingTask->id);
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