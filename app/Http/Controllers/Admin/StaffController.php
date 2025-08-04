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
            Staff::class,
            CreateStaffDTO::class
        );
    }

   
}
