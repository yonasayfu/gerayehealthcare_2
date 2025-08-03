<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Base\BaseController;
use App\Services\StaffService;
use App\Models\Staff;
use App\Services\Validation\Rules\StaffRules;

class StaffController extends BaseController
{
    public function __construct(StaffService $staffService)
    {
        parent::__construct(
            $staffService,
            StaffRules::class,
            'Admin/Staff',
            'staff',
            Staff::class
        );
    }

    public function show(Staff $staff)
    {
        return parent::show($staff->id);
    }

    public function edit(Staff $staff)
    {
        return parent::edit($staff->id);
    }

    public function update(Request $request, Staff $staff)
    {
        return parent::update($request, $staff->id);
    }

    public function destroy(Staff $staff)
    {
        return parent::destroy($staff->id);
    }
}
