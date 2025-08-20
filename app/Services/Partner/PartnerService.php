<?php

namespace App\Services\Partner;

use App\Http\Config\ExportConfig;
use App\Http\Traits\ExportableTrait;
use App\Models\Partner;
use App\Services\BaseService;

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
}
