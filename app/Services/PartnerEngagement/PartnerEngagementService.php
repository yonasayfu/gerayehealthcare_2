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
        return $query->where(function ($q) use ($search) {
            // Base fields
            $q->where('engagement_type', 'ilike', "%{$search}%")
              ->orWhere('summary', 'ilike', "%{$search}%");

            // Related Partner name
            $q->orWhereHas('partner', function ($qp) use ($search) {
                $qp->where('name', 'ilike', "%{$search}%");
            });

            // Related Staff first/last name
            $q->orWhereHas('staff', function ($qs) use ($search) {
                $qs->where('first_name', 'ilike', "%{$search}%")
                   ->orWhere('last_name', 'ilike', "%{$search}%");
            });
        });
    }

    protected function getExportConfig(): array
    {
        return ExportConfig::getPartnerEngagementConfig();
    }
}
