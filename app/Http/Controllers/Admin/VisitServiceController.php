<?php

namespace App\Http\Controllers\Admin;

use App\DTOs\CreateVisitServiceDTO;
use App\Http\Controllers\Base\BaseController;
use App\Http\Traits\ExportableTrait;
use App\Models\Patient;
use App\Models\Staff;
use App\Models\VisitService;
use App\Services\Validation\Rules\VisitServiceRules;
use App\Services\VisitServiceService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class VisitServiceController extends BaseController
{
    use ExportableTrait;

    public function __construct(VisitServiceService $visitServiceService)
    {
        parent::__construct(
            $visitServiceService,
            VisitServiceRules::class,
            'Admin/VisitServices',
            'visitServices',
            VisitService::class,
            CreateVisitServiceDTO::class
        );
    }

    public function create()
    {
        $patients = Patient::select('id', 'full_name')->orderBy('full_name')->get();
        $staff = Staff::select('id', 'first_name', 'last_name')->orderBy('first_name')->get();

        return Inertia::render($this->viewName.'/Create', [
            'patients' => $patients,
            'staff' => $staff,
        ]);
    }

    public function edit($id)
    {
        $visitService = $this->service->getById($id);
        $patients = Patient::select('id', 'full_name')->orderBy('full_name')->get();
        $staff = Staff::select('id', 'first_name', 'last_name')->orderBy('first_name')->get();

        return Inertia::render($this->viewName.'/Edit', [
            lcfirst(class_basename($this->modelClass)) => $visitService,
            'patients' => $patients,
            'staff' => $staff,
        ]);
    }

    public function export(Request $request)
    {
        return $this->handleExport($request, VisitService::class, \App\Http\Config\ExportConfig::getVisitServiceConfig());
    }

    public function printAll(Request $request)
    {
        return $this->handlePrintAll($request, VisitService::class, \App\Http\Config\ExportConfig::getVisitServiceConfig());
    }

    public function printCurrent(Request $request)
    {
        return $this->handlePrintCurrent($request, VisitService::class, \App\Http\Config\ExportConfig::getVisitServiceConfig());
    }

    public function printSingle(Request $request, VisitService $visit_service)
    {
        return $this->handlePrintSingle($request, $visit_service, \App\Http\Config\ExportConfig::getVisitServiceConfig());
    }
}
