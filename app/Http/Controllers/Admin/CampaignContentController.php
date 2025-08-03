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
            CampaignContent::class
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

    public function create()
    {
        $this->authorize('create', CampaignContent::class);
        return Inertia::render('Admin/CampaignContents/Create', [
            'campaigns' => MarketingCampaign::all(),
            'platforms' => MarketingPlatform::all(),
            'contentTypes' => ['Text', 'Image', 'Video', 'Article', 'Post'],
            'statuses' => ['Draft', 'Scheduled', 'Posted', 'Failed'],
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', CampaignContent::class);
        return parent::store($request);
    }

    public function show(CampaignContent $campaignContent)
    {
        $this->authorize('view', $campaignContent);
        return parent::show($campaignContent->id);
    }

    public function edit(CampaignContent $campaignContent)
    {
        $this->authorize('update', $campaignContent);
        $data = $this->service->getById($campaignContent->id);
        return Inertia::render('Admin/CampaignContents/Edit', [
            'campaignContent' => $data,
            'campaigns' => MarketingCampaign::all(),
            'platforms' => MarketingPlatform::all(),
            'contentTypes' => ['Text', 'Image', 'Video', 'Article', 'Post'],
            'statuses' => ['Draft', 'Scheduled', 'Posted', 'Failed'],
        ]);
    }

    public function update(Request $request, CampaignContent $campaignContent)
    {
        $this->authorize('update', $campaignContent);
        return parent::update($request, $campaignContent->id);
    }

    public function destroy(CampaignContent $campaignContent)
    {
        $this->authorize('delete', $campaignContent);
        return parent::destroy($campaignContent->id);
    }

    public function export(Request $request)
    {
        $this->authorize('viewAny', CampaignContent::class);
        return parent::export($request);
    }

    public function printAll(Request $request)
    {
        $this->authorize('viewAny', CampaignContent::class);
        return parent::printAll($request);
    }

    public function printCurrent(Request $request)
    {
        $this->authorize('viewAny', CampaignContent::class);
        return parent::printCurrent($request);
    }
}
