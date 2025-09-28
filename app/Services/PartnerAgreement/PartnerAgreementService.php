<?php

namespace App\Services\PartnerAgreement;

use App\Http\Config\ExportConfig;
use App\Http\Traits\ExportableTrait;
use App\Models\PartnerAgreement;
use App\Services\Base\BaseService;
use Illuminate\Http\Request;

class PartnerAgreementService extends BaseService
{
    use ExportableTrait;

    public function __construct(PartnerAgreement $model)
    {
        parent::__construct($model);
    }

    protected function applySearch($query, $search)
    {
        return $query->where('agreement_title', 'like', "%{$search}%")
            ->orWhere('agreement_type', 'like', "%{$search}%");
    }

    protected function getExportConfig(): array
    {
        return ExportConfig::getPartnerAgreementConfig();
    }

    // Public API used by PartnerAgreementController
    public function export(Request $request)
    {
        return $this->handleExport($request, PartnerAgreement::class, $this->getExportConfig());
    }

    public function printAll(Request $request)
    {
        return $this->handlePrintAll($request, PartnerAgreement::class, $this->getExportConfig());
    }

    public function printCurrent(Request $request)
    {
        return $this->handlePrintCurrent($request, PartnerAgreement::class, $this->getExportConfig());
    }

    public function printSingle(Request $request, $id)
    {
        $agreement = PartnerAgreement::findOrFail($id);

        return $this->handlePrintSingle($request, $agreement, $this->getExportConfig());
    }
}
