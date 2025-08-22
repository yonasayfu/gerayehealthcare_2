<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Base\BaseController;
use App\Services\StaffService;
use App\Models\Staff;
use App\Services\Validation\Rules\StaffRules;
use App\DTOs\CreateStaffDTO;
use Inertia\Inertia;
use Illuminate\Http\Request;

class StaffController extends BaseController
{
    public function __construct(StaffService $staffService)
    {
        parent::__construct(
            $staffService,
            StaffRules::class,
            'Admin/Staff',
            'staff',
            Staff::class,
            CreateStaffDTO::class
        );
    }

    public function create()
    {
        return Inertia::render('Admin/Staff/Create', [
            'departments' => config('hr.departments'),
            'positions' => config('hr.positions'),
        ]);
    }

    public function export(Request $request)
    {
        return app(StaffService::class)->export($request);
    }

    public function edit($id)
    {
        $staff = $this->service->getById($id);
        return Inertia::render('Admin/Staff/Edit', [
            'staff' => $staff,
            'departments' => config('hr.departments'),
            'positions' => config('hr.positions'),
        ]);
    }
   
    public function printAll()
    {
        return app(StaffService::class)->printAll(request());
    }

    public function printCurrent()
    {
        return app(StaffService::class)->printCurrent(request());
    }

    public function printSingle(Staff $staff)
    {
        return app(StaffService::class)->printSingle($staff, request());
    }

    public function update(Request $request, $id)
    {
        $staff = $this->service->getById($id);
        $validatedData = $this->validateRequest($request, 'update', $staff);

        $this->service->update($id, $validatedData);

        return redirect()->route('admin.staff.index')->with('banner', 'Staff updated successfully.')->with('bannerStyle', 'success');
    }
}
