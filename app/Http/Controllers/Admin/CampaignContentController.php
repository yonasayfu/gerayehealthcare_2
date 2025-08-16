<?php

namespace App\Http\Controllers\Admin;

use App\Models\MarketingCampaign;
use App\Models\MarketingPlatform;
use App\Services\Validation\Rules\CampaignContentRules;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Controllers\Base\BaseController;
use App\Services\CampaignContentService;
use App\Models\CampaignContent;
use App\DTOs\CreateCampaignContentDTO;

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
        $campaignContent = $this->service->getById($id);
        $campaigns = MarketingCampaign::select('id', 'campaign_name')->orderBy('campaign_name')->get();
        $platforms = MarketingPlatform::select('id', 'name')->orderBy('name')->get();

        return Inertia::render($this->viewName . '/Edit', [
            lcfirst(class_basename($this->modelClass)) => $campaignContent,
            'campaigns' => $campaigns,
            'platforms' => $platforms,
        ]);
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
