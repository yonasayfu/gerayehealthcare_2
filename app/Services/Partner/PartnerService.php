<?php

namespace App\Services\Partner;

use App\Http\Config\ExportConfig;
use App\Http\Traits\ExportableTrait;
use App\Models\Partner;
use App\Services\Base\BaseService;
use Illuminate\Http\Request;

class PartnerService extends BaseService
{
    use ExportableTrait;

    public function __construct(Partner $model)
    {
        parent::__construct($model);
    }

    protected function applySearch($query, $search)
    {
        return $query->where('name', 'like', "%{$search}%")
            ->orWhere('type', 'like', "%{$search}%")
            ->orWhere('engagement_status', 'like', "%{$search}%");
    }

    protected function getExportConfig(): array
    {
        return ExportConfig::getPartnerConfig();
    }

    // Public API used by PartnerController
    public function export(Request $request)
    {
        return $this->handleExport($request, Partner::class, $this->getExportConfig());
    }

    public function printAll(Request $request)
    {
        return $this->handlePrintAll($request, Partner::class, $this->getExportConfig());
    }

    public function printCurrent(Request $request)
    {
        return $this->handlePrintCurrent($request, Partner::class, $this->getExportConfig());
    }

    public function printSingle(Request $request, $id)
    {
        $partner = Partner::findOrFail($id);

        return $this->handlePrintSingle($request, $partner, $this->getExportConfig());
    }
}
