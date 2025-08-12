<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Base\BaseController;
use App\Services\StaffService;
use App\Models\Staff;
use App\Services\Validation\Rules\StaffRules;
use App\DTOs\CreateStaffDTO;
use App\DTOs\UpdateStaffDTO;
use Inertia\Inertia;

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
            CreateStaffDTO::class,
            UpdateStaffDTO::class
        );
    }

    public function create()
    {
        return Inertia::render('Admin/Staff/Create', [
            'departments' => config('hr.departments'),
        ]);
    }

    public function edit($id)
    {
        $staff = $this->service->getById($id);
        return Inertia::render('Admin/Staff/Edit', [
            'staff' => $staff,
            'departments' => config('hr.departments'),
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
}
