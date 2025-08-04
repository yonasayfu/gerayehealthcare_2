<?php

namespace App\Services;

use App\DTOs\CreateMarketingBudgetDTO;
use App\Models\MarketingBudget;
use Illuminate\Http\Request;
use App\Http\Traits\ExportableTrait;
use App\Http\Config\AdditionalExportConfigs;

class MarketingBudgetService extends BaseService
{
    use ExportableTrait;

    public function __construct(MarketingBudget $marketingBudget)
    {
        parent::__construct($marketingBudget);
    }

    protected function applySearch($query, $search)
    {
        return $query->where('budget_name', 'ilike', "%{$search}%");
    }

    public function getAll(Request $request)
    {
        $query = $this->model->with(['campaign', 'platform']);

        if ($request->has('search')) {
            $this->applySearch($query, $request->input('search'));
        }

        if ($request->filled('campaign_id')) {
            $query->where('campaign_id', $request->input('campaign_id'));
        }
        if ($request->filled('platform_id')) {
            $query->where('platform_id', $request->input('platform_id'));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }
        if ($request->filled('period_start')) {
            $query->where('period_start', '>=', $request->input('period_start'));
        }
        if ($request->filled('period_end')) {
            $query->where('period_end', '<=', $request->input('period_end'));
        }

        if ($request->has('sort')) {
            $direction = $request->input('direction', 'asc');
            $query->orderBy($request->input('sort'), $direction);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        return $query->paginate($request->input('per_page', 10));
    }

    public function export(Request $request)
    {
        return $this->handleExport($request, MarketingBudget::class, AdditionalExportConfigs::getMarketingBudgetConfig());
    }

    public function printAll(Request $request)
    {
        return $this->handlePrintAll($request, MarketingBudget::class, AdditionalExportConfigs::getMarketingBudgetConfig());
    }

    public function printCurrent(Request $request)
    {
        return $this->handlePrintCurrent($request, MarketingBudget::class, AdditionalExportConfigs::getMarketingBudgetConfig());
    }
}
