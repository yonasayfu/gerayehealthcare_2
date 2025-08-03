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
            StaffAvailability::class
        );
    }

    public function getCalendarEvents(Request $request)
    {
        $validated = $request->validate(GetCalendarEventsRules::get());
        $events = $this->service->getCalendarEvents($request);
        return response()->json($events);
    }

    public function index(Request $request)
    {
        $data = $this->service->getAll($request);
        return Inertia::render('Admin/StaffAvailabilities/Index', [
            'availabilities' => $data,
            'filters' => $request->only(['staff_id', 'status', 'start_date', 'end_date', 'sort', 'direction']),
            'staffList' => Staff::orderBy('first_name')->get(['id', 'first_name', 'last_name']),
        ]);
    }

    public function update(Request $request, StaffAvailability $availability)
    {
        try {
            return parent::update($request, $availability->id);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function destroy(StaffAvailability $availability)
    {
        return parent::destroy($availability->id);
    }

    public function store(Request $request)
    {
        try {
            return parent::store($request);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }
}