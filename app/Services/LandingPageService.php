<?php

namespace App\Services;

use App\Models\LandingPage;
use Illuminate\Http\Request;
use App\Http\Traits\ExportableTrait;
use App\Http\Config\AdditionalExportConfigs;

class LandingPageService extends BaseService
{
    use ExportableTrait;

    public function __construct(LandingPage $landingPage)
    {
        parent::__construct($landingPage);
    }

    protected function applySearch($query, $search)
    {
        $query->where('page_title', 'ilike', "%{$search}%")
              ->orWhere('page_url', 'ilike', "%{$search}%")
              ->orWhere('page_code', 'ilike', "%{$search}%");
    }

    public function getAll(Request $request)
    {
        $query = $this->model->with(['campaign']);

        if ($request->has('search')) {
            $this->applySearch($query, $request->input('search'));
        }

        if ($request->filled('campaign_id')) {
            $query->where('campaign_id', $request->input('campaign_id'));
        }
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->input('is_active'));
        }
        if ($request->filled('language')) {
            $query->where('language', $request->input('language'));
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
        return $this->handleExport($request, LandingPage::class, AdditionalExportConfigs::getLandingPageConfig());
    }

    public function printAll(Request $request)
    {
        return $this->handlePrintAll($request, LandingPage::class, AdditionalExportConfigs::getLandingPageConfig());
    }

    public function printCurrent(Request $request)
    {
        return $this->handlePrintCurrent($request, LandingPage::class, AdditionalExportConfigs::getLandingPageConfig());
    }
}
