<?php

namespace App\Services;

use App\DTOs\CreateLeadSourceDTO;
use App\Models\LeadSource;
use Illuminate\Http\Request;
use App\Http\Traits\ExportableTrait;
use App\Http\Config\AdditionalExportConfigs;

class LeadSourceService extends BaseService
{
    use ExportableTrait;

    public function __construct(LeadSource $leadSource)
    {
        parent::__construct($leadSource);
    }

    protected function applySearch($query, $search)
    {
        $query->where('name', 'ilike', "%{$search}%")
              ->orWhere('category', 'ilike', "%{$search}%");
    }

    public function getAll(Request $request, array $with = [])
    {
        $query = $this->model->with($with);

        if ($request->has('search')) {
            $this->applySearch($query, $request->input('search'));
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->input('is_active'));
        }

        if ($request->has('sort')) {
            $direction = $request->input('direction', 'asc');
            $query->orderBy($request->input('sort'), $direction);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        return $query->paginate($request->input('per_page', 10));
    }

    public function toggleStatus(LeadSource $leadSource): LeadSource
    {
        $leadSource->is_active = !$leadSource->is_active;
        $leadSource->save();
        return $leadSource;
    }

    public function export(Request $request)
    {
        return $this->handleExport($request, LeadSource::class, AdditionalExportConfigs::getLeadSourceConfig());
    }

    public function printAll(Request $request)
    {
        return $this->handlePrintAll($request, LeadSource::class, AdditionalExportConfigs::getLeadSourceConfig());
    }

    public function printSingle($id)
    {
        $leadSource = $this->getById($id);
        return $this->handlePrintSingle($leadSource, AdditionalExportConfigs::getLeadSourceConfig());
    }

    public function printCurrent(Request $request)
    {
        return $this->handlePrintCurrent($request, LeadSource::class, AdditionalExportConfigs::getLeadSourceConfig());
    }
}
