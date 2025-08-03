<?php

namespace App\Http\Controllers\Admin;

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
            VisitService::class
        );
    }

    public function create()
    {
        return Inertia::render('Admin/VisitServices/Create', [
            'patients' => Patient::orderBy('full_name')->get(['id', 'full_name']),
            'staff' => Staff::where('status', 'Active')->orderBy('first_name')->get(['id', 'first_name', 'last_name']),
        ]);
    }

    public function store(Request $request)
    {
        try {
            return parent::store($request);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function show(VisitService $visitService)
    {
        return parent::show($visitService->id);
    }

    public function edit(VisitService $visitService)
    {
        $data = $this->service->getById($visitService->id);
        return Inertia::render('Admin/VisitServices/Edit', [
            'visitService' => $data,
            'patients' => Patient::orderBy('full_name')->get(['id', 'full_name']),
            'staff' => Staff::where('status', 'Active')->orderBy('first_name')->get(['id', 'first_name', 'last_name']),
        ]);
    }

    public function update(Request $request, VisitService $visitService)
    {
        try {
            return parent::update($request, $visitService->id);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function destroy(VisitService $visitService)
    {
        return parent::destroy($visitService->id);
    }

    public function export(Request $request)
    {
        return parent::export($request);
    }
}
