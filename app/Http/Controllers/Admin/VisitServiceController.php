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

    

    public function create()
    {
        $patients = Patient::select('id', 'full_name')->orderBy('full_name')->get();
        $staff = Staff::select('id', 'first_name', 'last_name')->orderBy('first_name')->get();
        
        return Inertia::render($this->viewName . '/Create', [
            'patients' => $patients,
            'staff' => $staff
        ]);
    }

    public function printAll(Request $request)
    {
        return $this->service->printAll($request);
    }

    public function printCurrent(Request $request)
    {
        return $this->service->printCurrent($request);
    }

    public function printSingle($id, Request $request)
    {
        return $this->service->printSingle($id, $request);
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
