<?php

namespace App\Services;

use App\Models\MarketingCampaign;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Traits\ExportableTrait;
use App\Http\Config\ExportConfig;

class MarketingCampaignService extends BaseService
{
    use ExportableTrait;

    public function __construct(MarketingCampaign $marketingCampaign)
    {
        parent::__construct($marketingCampaign);
    }

    protected function applySearch($query, $search)
    {
        $query->where('campaign_name', 'ilike', "%{$search}%")
              ->orWhere('campaign_code', 'ilike', "%{$search}%")
              ->orWhere('utm_campaign', 'ilike', "%{$search}%");
    }

    public function getAll(Request $request, array $with = [])
    {
        $query = $this->model->with(array_merge(['platform', 'assignedStaff', 'responsibleStaff', 'createdByStaff'], $with));

        if ($request->has('search')) {
            $this->applySearch($query, $request->input('search'));
        }

        if ($request->filled('platform_id')) {
            $query->where('platform_id', $request->input('platform_id'));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }
        if ($request->filled('campaign_type')) {
            $query->where('campaign_type', $request->input('campaign_type'));
        }
        if ($request->filled('start_date')) {
            $query->where('start_date', '>=', $request->input('start_date'));
        }
        if ($request->filled('end_date')) {
            $query->where('end_date', '<=', $request->input('end_date'));
        }

        if ($request->has('sort')) {
            $direction = $request->input('direction', 'asc');
            $query->orderBy($request->input('sort'), $direction);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        return $query->paginate($request->input('per_page', 10));
    }

    public function create(array|object $data): MarketingCampaign
    {
        $data = is_object($data) ? (array) $data : $data;
        return parent::create($data);
    }

    public function getById(int $id, array $with = []): MarketingCampaign
    {
        $with = array_unique(array_merge(['platform', 'assignedStaff', 'responsibleStaff', 'createdByStaff'], $with));
        return $this->model->with($with)->findOrFail($id);
    }

    public function printAll(Request $request)
    {
        return $this->handlePrintAll($request, MarketingCampaign::class, ExportConfig::getMarketingCampaignConfig());
    }

    public function printCurrent(Request $request)
    {
        return $this->handlePrintCurrent($request, MarketingCampaign::class, ExportConfig::getMarketingCampaignConfig());
    }

    public function printSingle(Request $request, MarketingCampaign $marketingCampaign)
    {
        return $this->handlePrintSingle($request, $marketingCampaign, ExportConfig::getMarketingCampaignConfig());
    }
}
