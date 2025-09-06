<?php

namespace App\Http\Controllers\Admin;

use App\Http\Config\AdditionalExportConfigs;
use App\Http\Controllers\Base\BaseController;
use App\Http\Traits\ExportableTrait;
use App\Models\Prescription;
use App\Services\PrescriptionService;
use App\Services\Validation\Rules\PrescriptionRules;
use Illuminate\Http\Request;

class PrescriptionController extends BaseController
{
    use ExportableTrait;

    public function __construct(PrescriptionService $service)
    {
        parent::__construct(
            $service,
            PrescriptionRules::class,
            'Admin/Prescriptions',
            'prescriptions',
            Prescription::class
        );
    }

    public function export(Request $request)
    {
        $config = AdditionalExportConfigs::getPrescriptionConfig();

        return $this->handleExport($request, Prescription::class, $config);
    }

    public function printAll(Request $request)
    {
        $config = AdditionalExportConfigs::getPrescriptionConfig();

        return $this->handlePrintAll($request, Prescription::class, $config);
    }

    public function printCurrent(Request $request)
    {
        $config = AdditionalExportConfigs::getPrescriptionConfig();

        return $this->handlePrintCurrent($request, Prescription::class, $config);
    }

    public function printSingle(Request $request, $id)
    {
        $model = Prescription::with('items')->findOrFail($id);
        $config = AdditionalExportConfigs::getPrescriptionConfig();

        return $this->handlePrintSingle($request, $model, $config);
    }
}
