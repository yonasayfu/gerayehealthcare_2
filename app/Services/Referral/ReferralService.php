<?php

namespace App\Services\Referral;

use App\Http\Config\ExportConfig;
use App\Http\Traits\ExportableTrait;
use App\Models\Referral;
use App\Services\Base\BaseService;
use Illuminate\Http\Request;

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

    // Public API used by ReferralController
    public function export(Request $request)
    {
        return $this->handleExport($request, Referral::class, $this->getExportConfig());
    }

    public function printCurrent(Request $request)
    {
        return $this->handlePrintCurrent($request, Referral::class, $this->getExportConfig());
    }

    public function printSingle(Request $request, $id)
    {
        $referral = Referral::findOrFail($id);

        return $this->handlePrintSingle($request, $referral, $this->getExportConfig());
    }
}
