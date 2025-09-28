<?php

namespace App\Services\LandingPage;

use App\Http\Config\ExportConfig;
use App\Http\Traits\ExportableTrait;
use App\Models\LandingPage;
use App\Services\Base\BaseService;
use Illuminate\Http\Request;

class LandingPageService extends BaseService
{
    use ExportableTrait;

    public function __construct(LandingPage $landingPage)
    {
        parent::__construct($landingPage);
    }

    public function export(Request $request)
    {
        // Delegate to the shared export handler with LandingPage config
        return $this->handleExport($request, LandingPage::class, \App\Http\Config\ExportConfig::getLandingPageConfig());
    }

    protected function applySearch($query, $search)
    {
        $query->where('page_title', 'ilike', "%{$search}%")
            ->orWhere('page_url', 'ilike', "%{$search}%")
            ->orWhere('page_code', 'ilike', "%{$search}%");
    }

    public function getAll(Request $request, array $with = [])
    {
        // Base query with left join to expose campaign_name for flat access in lists
        $query = $this->model
            ->newQuery()
            ->leftJoin('marketing_campaigns', 'marketing_campaigns.id', '=', 'landing_pages.campaign_id')
            ->select('landing_pages.*', 'marketing_campaigns.campaign_name as campaign_name')
            ->with(array_merge(['campaign' => function ($q) {
                $q->select('id', 'campaign_name');
            }], $with));

        if ($request->has('search')) {
            $this->applySearch($query, $request->input('search'));
        }

        if ($request->filled('campaign_id')) {
            $query->where('landing_pages.campaign_id', $request->input('campaign_id'));
        }
        if ($request->filled('is_active')) {
            $query->where('landing_pages.is_active', $request->input('is_active'));
        }
        if ($request->filled('language')) {
            $query->where('landing_pages.language', $request->input('language'));
        }

        if ($request->has('sort')) {
            $direction = $request->input('direction', 'asc');
            $sortColumn = $request->input('sort');
            // Allow sorting by campaign_name safely
            if ($sortColumn === 'campaign_id') {
                $sortColumn = 'marketing_campaigns.campaign_name';
            }
            $query->orderBy($sortColumn, $direction);
        } else {
            $query->orderBy('landing_pages.created_at', 'desc');
        }

        return $query->paginate($request->input('per_page', 10))->withQueryString();
    }

    /**
     * Apply filters for export/print handlers
     */
    protected function applyFilters($query, Request $request)
    {
        if ($request->filled('campaign_id')) {
            $query->where('campaign_id', $request->input('campaign_id'));
        }
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->input('is_active'));
        }
        if ($request->filled('language')) {
            $query->where('language', $request->input('language'));
        }
    }

    public function printAll(Request $request)
    {
        return $this->handlePrintAll($request, LandingPage::class, ExportConfig::getLandingPageConfig());
    }

    public function printCurrent(Request $request)
    {
        return $this->handlePrintCurrent($request, LandingPage::class, ExportConfig::getLandingPageConfig());
    }

    public function printSingle(Request $request, LandingPage $landingPage)
    {
        return $this->handlePrintSingle($request, $landingPage, ExportConfig::getLandingPageConfig());
    }

    /**
     * Ensure campaign relation is always available on detail views
     */
    public function getById(int $id, array $with = [])
    {
        return parent::getById($id, array_merge(['campaign'], $with));
    }
}
