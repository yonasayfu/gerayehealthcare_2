<?php

namespace App\Services\PartnerCommission;

use App\Http\Config\ExportConfig;
use App\Http\Traits\ExportableTrait;
use App\Models\PartnerCommission;
use App\Services\BaseService;

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
}
