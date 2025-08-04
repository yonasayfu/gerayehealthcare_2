<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Base\BaseController;
use App\Services\CampaignContentService;
use App\Models\CampaignContent;
use App\Models\MarketingCampaign;
use App\Models\MarketingPlatform;
use App\Services\Validation\Rules\CampaignContentRules;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CampaignContentController extends BaseController
{
    use AuthorizesRequests;

    public function __construct(CampaignContentService $campaignContentService)
    {
        parent::__construct(
            $campaignContentService,
            CampaignContentRules::class,
            'Admin/CampaignContents',
            'campaignContents',
            CampaignContent::class,
            CreateCampaignContentDTO::class
        );
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', CampaignContent::class);
        $data = $this->service->getAll($request);
        return Inertia::render($this->viewName . '/Index', [
            $this->dataVariableName => $data,
            'filters' => $request->only(['search', 'sort', 'direction', 'per_page', 'campaign_id', 'platform_id', 'content_type', 'status', 'scheduled_post_date_start', 'scheduled_post_date_end']),
            'campaigns' => MarketingCampaign::all(),
            'platforms' => MarketingPlatform::all(),
        ]);
    }

    

    

    

    
}
