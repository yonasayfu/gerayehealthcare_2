<?php

namespace App\Services\PartnerCommission;

use App\Http\Config\ExportConfig;
use App\Http\Traits\ExportableTrait;
use App\Models\PartnerCommission;
use App\Services\Base\BaseService;
use Illuminate\Http\Request;

class PartnerCommissionService extends BaseService
{
    use ExportableTrait;

    public function __construct(PartnerCommission $model)
    {
        parent::__construct($model);
    }

    protected function applySearch($query, $search)
    {
        return $query->where('status', 'like', "%{$search}%")
            ->orWhere('calculation_date', 'like', "%{$search}%");
    }

    protected function getExportConfig(): array
    {
        return ExportConfig::getPartnerCommissionConfig();
    }

    public function export(Request $request)
    {
        return $this->handleExport($request, PartnerCommission::class, $this->getExportConfig());
    }

    public function printAll(Request $request)
    {
        return $this->handlePrintAll($request, PartnerCommission::class, $this->getExportConfig());
    }

    public function printCurrent(Request $request)
    {
        return $this->handlePrintCurrent($request, PartnerCommission::class, $this->getExportConfig());
    }

    public function printSingle(Request $request, $id)
    {
        $model = PartnerCommission::findOrFail($id);

        return $this->handlePrintSingle($request, $model, $this->getExportConfig());
    }
}
