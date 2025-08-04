<?php

namespace App\Services;

use App\DTOs\CreateCampaignContentDTO;
use App\Models\CampaignContent;
use Illuminate\Http\Request;
use App\Http\Traits\ExportableTrait;
use App\Http\Config\AdditionalExportConfigs;

class CampaignContentService extends BaseService
{
    use ExportableTrait;

    public function __construct(CampaignContent $campaignContent)
    {
        parent::__construct($campaignContent);
    }

    protected function applySearch($query, $search)
    {
        $query->where('title', 'ilike', "%{$search}%")
              ->orWhere('description', 'ilike', "%{$search}%");
    }

    public function getAll(Request $request)
    {
        $query = $this->model->query();

        if ($request->has('search')) {
            $this->applySearch($query, $request->input('search'));
        }

        if ($request->filled('campaign_id')) {
            $query->where('campaign_id', $request->input('campaign_id'));
        }
        if ($request->filled('platform_id')) {
            $query->where('platform_id', $request->input('platform_id'));
        }
        if ($request->filled('content_type')) {
            $query->where('content_type', $request->input('content_type'));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }
        if ($request->filled('scheduled_post_date_start')) {
            $query->where('scheduled_post_date', '>=', $request->input('scheduled_post_date_start'));
        }
        if ($request->filled('scheduled_post_date_end')) {
            $query->where('scheduled_post_date', '<=', $request->input('scheduled_post_date_end'));
        }

        if ($request->has('sort')) {
            $direction = $request->input('direction', 'asc');
            $query->orderBy($request->input('sort'), $direction);
        }

        return $query->with(['campaign', 'platform'])->paginate($request->input('per_page', 10));
    }

    public function export(Request $request)
    {
        return $this->handleExport($request, CampaignContent::class, AdditionalExportConfigs::getCampaignContentConfig());
    }

    public function printAll(Request $request)
    {
        return $this->handlePrintAll($request, CampaignContent::class, AdditionalExportConfigs::getCampaignContentConfig());
    }

    public function printCurrent(Request $request)
    {
        return $this->handlePrintCurrent($request, CampaignContent::class, AdditionalExportConfigs::getCampaignContentConfig());
    }
}
