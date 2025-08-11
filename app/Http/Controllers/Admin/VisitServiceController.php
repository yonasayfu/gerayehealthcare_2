<?php

namespace App\Http\Controllers\Admin;

use App\DTOs\CreateVisitServiceDTO;
use App\DTOs\UpdateVisitServiceDTO;
use App\Http\Controllers\Base\BaseController;
use App\Services\VisitServiceService;
use App\Models\VisitService;
use App\Models\Patient;
use App\Models\Staff;
use App\Services\Validation\Rules\VisitServiceRules;
use Illuminate\Http\Request;
use Inertia\Inertia;

class VisitServiceController extends BaseController
{
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

    public function printAll(Request $request)
    {
        // Use the export/print pipeline to generate a PDF preview in browser
        return $this->service->printAll($request);
    }

    public function printCurrent(Request $request)
    {
        // Keep existing behavior (if any) but index page will trigger in-page print without new tab
        return Inertia::render($this->viewName . '/Index', [
            $this->dataVariableName => $this->service->getAll($request),
            'filters' => $request->only(['search', 'sort', 'direction', 'per_page', 'sort_by', 'sort_order'])
        ]);
    }

    public function create()
    {
        $patients = Patient::select('id', 'full_name')->orderBy('full_name')->get();
        $staff = Staff::select('id', 'first_name', 'last_name')->orderBy('first_name')->get();
        
        return Inertia::render($this->viewName . '/Create', [
            'patients' => $patients,
            'staff' => $staff
        ]);
    }

    public function edit($id)
    {
        $visitService = $this->service->getById($id);
        $patients = Patient::select('id', 'full_name')->orderBy('full_name')->get();
        $staff = Staff::select('id', 'first_name', 'last_name')->orderBy('first_name')->get();
        
        return Inertia::render($this->viewName . '/Edit', [
            lcfirst(class_basename($this->modelClass)) => $visitService,
            'patients' => $patients,
            'staff' => $staff
        ]);
    }
}
