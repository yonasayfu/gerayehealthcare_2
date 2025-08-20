<?php

namespace App\Services\Referral;

use App\Http\Config\ExportConfig;
use App\Http\Traits\ExportableTrait;
use App\Models\Referral;
use App\Services\BaseService;

class ReferralService extends BaseService
{
    use ExportableTrait;

    public function __construct(Referral $model)
    {
        parent::__construct($model);
    }

    protected function applySearch($query, $search)
    {
        return $query->where('status', 'like', "%{$search}%")
            ->orWhere('referral_date', 'like', "%{$search}%");
    }

    protected function getExportConfig(): array
    {
        return ExportConfig::getReferralConfig();
    }
}
