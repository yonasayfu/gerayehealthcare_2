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
            MarketingTask::class,
            CreateMarketingTaskDTO::class
        );
    }

    

    
}