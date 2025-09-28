<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Base\BaseController;
use App\Models\CampaignContent;
use App\Models\MarketingCampaign;
use App\Models\MarketingPlatform;
use App\Services\CampaignContent\CampaignContentService;
use App\Services\Validation\Rules\CampaignContentRules;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Inertia\Inertia;

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
            null// Use full validated payload; DTO is incomplete and mismatched
        );
    }

    public function create()
    {
        $campaigns = MarketingCampaign::select('id', 'campaign_name')->orderBy('campaign_name')->get();
        $platforms = MarketingPlatform::select('id', 'name')->orderBy('name')->get();
        $contentTypes = ['text', 'image', 'video'];
        $statuses = ['draft', 'scheduled', 'posted', 'failed'];

        return Inertia::render($this->viewName . '/Create', [
            'campaigns' => $campaigns,
            'platforms' => $platforms,
            'contentTypes' => $contentTypes,
            'statuses' => $statuses,
        ]);
    }

    public function edit($id)
    {
        $campaignContent = $this->service->getById($id);
        $campaigns = MarketingCampaign::select('id', 'campaign_name')->orderBy('campaign_name')->get();
        $platforms = MarketingPlatform::select('id', 'name')->orderBy('name')->get();
        $contentTypes = ['text', 'image', 'video'];
        $statuses = ['draft', 'scheduled', 'posted', 'failed'];

        return Inertia::render($this->viewName . '/Edit', [
            lcfirst(class_basename($this->modelClass)) => $campaignContent,
            'campaigns' => $campaigns,
            'platforms' => $platforms,
            'contentTypes' => $contentTypes,
            'statuses' => $statuses,
        ]);
    }

    public function show($id)
    {
        $this->authorize('view', CampaignContent::class);
        // Eager-load relations so Show.vue can render names
        $campaignContent = $this->service->getById($id, ['campaign', 'platform']);

        return Inertia::render($this->viewName . '/Show', [
            lcfirst(class_basename($this->modelClass)) => $campaignContent,
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

    public function export(Request $request)
    {
        return $this->service->export($request);
    }
}
