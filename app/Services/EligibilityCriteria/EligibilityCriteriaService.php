<?php

namespace App\Services\EligibilityCriteria;

use App\Http\Config\AdditionalExportConfigs;
use App\Http\Traits\ExportableTrait;
use App\Models\EligibilityCriteria;
use App\Services\Base\BaseService;
use Illuminate\Http\Request;

class EligibilityCriteriaService extends BaseService
{
    use ExportableTrait;

    public function __construct(EligibilityCriteria $eligibilityCriteria)
    {
        parent::__construct($eligibilityCriteria);
    }

    public function create(array|object $data): EligibilityCriteria
    {
        $data = is_object($data) ? (array) $data : $data;

        return parent::create($data);
    }

    // CSV-only export to match Events behavior
    public function export(Request $request)
    {
        // Force CSV export only
        $request->merge(['type' => 'csv']);

        return $this->handleExport($request, EligibilityCriteria::class, AdditionalExportConfigs::getEligibilityCriteriaConfig());
    }

    public function printCurrent(Request $request)
    {
        return $this->handlePrintCurrent($request, EligibilityCriteria::class, AdditionalExportConfigs::getEligibilityCriteriaConfig());
    }

    public function printSingle(Request $request, EligibilityCriteria $eligibilityCriteria)
    {
        return $this->handlePrintSingle($request, $eligibilityCriteria, AdditionalExportConfigs::getEligibilityCriteriaConfig());
    }

    protected function applySearch($query, $search)
    {
        $query->where(function ($q) use ($search) {
            $q->where('criteria_title', 'ILIKE', "%{$search}%")
                ->orWhere('operator', 'ILIKE', "%{$search}%")
                ->orWhere('value', 'ILIKE', "%{$search}%");
        });
    }
}
