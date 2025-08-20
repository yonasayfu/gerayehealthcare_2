<?php

namespace App\Services\PartnerAgreement;

use App\Http\Config\ExportConfig;
use App\Http\Traits\ExportableTrait;
use App\Models\PartnerAgreement;
use App\Services\BaseService;

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
}
