<?php

namespace App\Http\Controllers\Admin;

use App\DTOs\CreateStaffAvailabilityDTO;
use App\Http\Controllers\Base\BaseController;
use App\Models\Staff;
use App\Models\StaffAvailability;
use App\Services\StaffAvailabilityService;
use App\Services\Validation\Rules\StaffAvailabilityRules;
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

    public function index(Request $request)
    {
        $data = $this->service->getAll($request);
        $staffList = Staff::select('id', 'first_name', 'last_name')->orderBy('first_name')->get();

        return Inertia::render('Admin/StaffAvailabilities/Index', [
            'availabilities' => $data,
            'filters' => $request->only(['staff_id', 'status', 'start_date', 'end_date', 'sort', 'direction', 'per_page']),
            'staffList' => $staffList,
        ]);
    }

    public function getCalendarEvents(Request $request)
    {
        return $this->service->getCalendarEvents($request);
    }

    public function availableStaff(Request $request)
    {
        $request->validate([
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
        ]);

        $available = $this->service->getAvailableStaff($request->input('start_time'), $request->input('end_time'));

        return response()->json($available);
    }
}
