<?php

namespace App\Services;

use App\DTOs\CreateMarketingLeadDTO;
use App\Models\MarketingLead;
use Illuminate\Http\Request;
use App\Http\Traits\ExportableTrait;
use App\Http\Config\AdditionalExportConfigs;

class MarketingLeadService extends BaseService
{
    use ExportableTrait;

    public function __construct(MarketingLead $marketingLead)
    {
        parent::__construct($marketingLead);
    }

    protected function applySearch($query, $search)
    {
        return $query->where('first_name', 'ilike', "%{$search}%")
                  ->orWhere('last_name', 'ilike', "%{$search}%")
                  ->orWhere('email', 'ilike', "%{$search}%")
                  ->orWhere('lead_code', 'ilike', "%{$search}%");
    }

    public function getAll(Request $request)
    {
        $query = $this->model->with(['sourceCampaign', 'landingPage', 'assignedStaff', 'convertedPatient']);

        if ($request->has('search')) {
            $this->applySearch($query, $request->input('search'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }
        if ($request->filled('source_campaign_id')) {
            $query->where('source_campaign_id', $request->input('source_campaign_id'));
        }
        if ($request->filled('landing_page_id')) {
            $query->where('landing_page_id', $request->input('landing_page_id'));
        }
        if ($request->filled('assigned_staff_id')) {
            $query->where('assigned_staff_id', $request->input('assigned_staff_id'));
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
        return $this->handleExport($request, MarketingLead::class, AdditionalExportConfigs::getMarketingLeadConfig());
    }

    public function printSingle($id)
    {
        $marketingLead = $this->getById($id);
        return $this->handlePrintSingle($marketingLead, AdditionalExportConfigs::getMarketingLeadConfig());
    }

    public function printAll(Request $request)
    {
        return $this->handlePrintAll($request, MarketingLead::class, AdditionalExportConfigs::getMarketingLeadConfig());
    }

    public function printCurrent(Request $request)
    {
        return $this->handlePrintCurrent($request, MarketingLead::class, AdditionalExportConfigs::getMarketingLeadConfig());
    }
}
