<?php

namespace App\Http\Controllers\Admin;

use App\DTOs\CreateMarketingTaskDTO;
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
            MarketingTask::class,
            CreateMarketingTaskDTO::class
        );
    }

    /**
     * Provide list data needed by filters on index (campaigns, staffs, task types).
     */
    public function index(Request $request)
    {
        $data = $this->service->getAll($request);

        $campaigns = MarketingCampaign::select('id', 'campaign_name')->orderBy('campaign_name')->get();
        $staffs = Staff::with('user:id,name')->select('id', 'user_id')->orderBy('id', 'asc')->get();

        // Task types and statuses: align with factory and UI options
        $taskTypes = [
            'Email Campaign',
            'Social Media Post',
            'Ad Creation',
            'Content Writing',
            'SEO Optimization',
        ];
        $filters = $request->only([
            'search', 'sort', 'direction', 'per_page',
            'campaign_id', 'assigned_to_staff_id', 'task_type',
        ]);

        return Inertia::render($this->viewName . '/Index', [
            $this->dataVariableName => $data,
            'campaigns' => $campaigns,
            'staffs' => $staffs,
            'taskTypes' => $taskTypes,
            'filters' => $filters,
        ]);
    }

    /**
     * Provide dropdown datasets for create form, similar to Patient module pattern.
     */
    public function create()
    {
        $campaigns = MarketingCampaign::select('id', 'campaign_name')->orderBy('campaign_name')->get();
        $staffMembers = Staff::with('user:id,name')->select('id', 'user_id')->orderBy('id', 'asc')->get();
        $contents = CampaignContent::select('id', 'title')->orderBy('title')->get();

        $taskTypes = [
            'Email Campaign',
            'Social Media Post',
            'Ad Creation',
            'Content Writing',
            'SEO Optimization',
        ];
        $statuses = ['Pending', 'In Progress', 'Completed', 'Cancelled'];

        return Inertia::render($this->viewName . '/Create', [
            'campaigns' => $campaigns,
            'staffMembers' => $staffMembers,
            'contents' => $contents,
            'taskTypes' => $taskTypes,
            'statuses' => $statuses,
        ]);
    }

    /**
     * Provide dropdown datasets and record for edit form.
     */
    public function edit($id)
    {
        $data = $this->service->getById($id);

        $campaigns = MarketingCampaign::select('id', 'campaign_name')->orderBy('campaign_name')->get();
        $staffs = Staff::with('user:id,name')->select('id', 'user_id')->orderBy('id', 'asc')->get();
        $campaignContents = CampaignContent::select('id', 'title')->orderBy('title')->get();

        $taskTypes = [
            'Email Campaign',
            'Social Media Post',
            'Ad Creation',
            'Content Writing',
            'SEO Optimization',
        ];
        $statuses = ['Pending', 'In Progress', 'Completed', 'Cancelled'];

        return Inertia::render($this->viewName . '/Edit', [
            'marketingTask' => $data,
            'campaigns' => $campaigns,
            'staffs' => $staffs,
            'campaignContents' => $campaignContents,
            'taskTypes' => $taskTypes,
            'statuses' => $statuses,
        ]);
    }

    /**
     * Print current view via centralized export system.
     */
    public function printCurrent(Request $request)
    {
        return $this->service->printCurrent($request);
    }

    /**
     * Print all records via centralized export system.
     */
    public function printAll(Request $request)
    {
        return $this->service->printAll($request);
    }
}