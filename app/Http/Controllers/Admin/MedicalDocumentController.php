<?php

namespace App\Http\Controllers\Admin;

use App\Http\Config\AdditionalExportConfigs;
use App\Http\Controllers\Base\BaseController;
use App\Http\Traits\ExportableTrait;
use App\Models\MedicalDocument;
use App\Services\MedicalDocumentService;
use App\Services\Validation\Rules\MedicalDocumentRules;
use Illuminate\Http\Request;

class MedicalDocumentController extends BaseController
{
    use ExportableTrait;

    public function __construct(MedicalDocumentService $service)
    {
        parent::__construct(
            $service,
            MedicalDocumentRules::class,
            'Admin/MedicalDocuments',
            'medicalDocuments',
            MedicalDocument::class
        );
    }

    public function export(Request $request)
    {
        $config = AdditionalExportConfigs::getMedicalDocumentConfig();

        return $this->handleExport($request, MedicalDocument::class, $config);
    }

    public function printAll(Request $request)
    {
        $config = AdditionalExportConfigs::getMedicalDocumentConfig();

        return $this->handlePrintAll($request, MedicalDocument::class, $config);
    }

    public function printCurrent(Request $request)
    {
        $config = AdditionalExportConfigs::getMedicalDocumentConfig();

        return $this->handlePrintCurrent($request, MedicalDocument::class, $config);
    }

    public function printSingle(Request $request, $id)
    {
        $model = MedicalDocument::findOrFail($id);
        $config = AdditionalExportConfigs::getMedicalDocumentConfig();

        return $this->handlePrintSingle($request, $model, $config);
    }
}
