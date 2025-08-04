<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Base\BaseController;
use App\Services\StaffAvailabilityService;
use App\Models\StaffAvailability;
use App\Models\Staff;
use App\Services\Validation\Rules\StaffAvailabilityRules;
use App\Services\Validation\Rules\GetCalendarEventsRules;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StaffAvailabilityController extends BaseController
{
    public function __construct(StaffAvailabilityService $staffAvailabilityService)
    {
        parent::__construct(
            $staffAvailabilityService,
            StaffAvailabilityRules::class,
            'Admin/StaffAvailabilities',
            'availabilities',
            StaffAvailability::class,
            CreateStaffAvailabilityDTO::class
        );
    }

    

    
}