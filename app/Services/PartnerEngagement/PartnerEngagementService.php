<?php

namespace App\Services\PartnerEngagement;

use App\Http\Config\ExportConfig;
use App\Http\Traits\ExportableTrait;
use App\Models\PartnerEngagement;
use App\Services\BaseService;

class PartnerEngagementService extends BaseService
{
    use ExportableTrait;

    public function __construct(PartnerEngagement $model)
    {
        parent::__construct($model);
    }

    protected function applySearch($query, $search)
    {
        return $query->where('engagement_type', 'like', "%{$search}%")
            ->orWhere('summary', 'like', "%{$search}%");
    }

    protected function getExportConfig(): array
    {
        return ExportConfig::getPartnerEngagementConfig();
    }
}
