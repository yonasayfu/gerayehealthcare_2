<?php

namespace App\Http\Controllers\Admin;

use App\DTOs\CreateStaffAvailabilityDTO;
use App\Http\Controllers\Base\BaseController;
use App\Models\Staff;
use App\Models\StaffAvailability;
use App\Models\VisitService;
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

    public function create()
    {
        $staffList = Staff::select('id', 'first_name', 'last_name')->orderBy('first_name')->get();

        return Inertia::render('Admin/StaffAvailabilities/Create', [
            'staffList' => $staffList,
        ]);
    }

    public function edit($id)
    {
        $availability = $this->service->getById($id);
        $staffList = Staff::select('id', 'first_name', 'last_name')->orderBy('first_name')->get();

        return Inertia::render('Admin/StaffAvailabilities/Edit', [
            'staffAvailability' => $availability,
            'staffList' => $staffList,
        ]);
    }

    public function show($id)
    {
        $availability = StaffAvailability::with('staff')->findOrFail($id);

        return Inertia::render('Admin/StaffAvailabilities/Show', [
            'staffAvailability' => $availability,
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

    public function visitConflicts(Request $request)
    {
        $validated = $request->validate([
            'staff_id' => 'required|exists:staff,id',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
        ]);

        $count = VisitService::where('staff_id', $validated['staff_id'])
            ->where('status', '!=', 'Cancelled')
            ->whereBetween('scheduled_at', [$validated['start_time'], $validated['end_time']])
            ->count();

        return response()->json([
            'hasConflicts' => $count > 0,
            'count' => $count,
        ]);
    }
}
