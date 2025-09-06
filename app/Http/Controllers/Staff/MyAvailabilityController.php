<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Base\BaseController;
use App\Http\Requests\GetEventsRequest;
use App\Models\StaffAvailability;
use App\Models\VisitService;
use App\Services\StaffAvailabilityService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class MyAvailabilityController extends BaseController
{
    protected $staffAvailabilityService;

    public function __construct(StaffAvailabilityService $staffAvailabilityService)
    {
        parent::__construct(
            $staffAvailabilityService,
            null,
            'Staff/MyAvailability',
            'myAvailabilities',
            StaffAvailability::class,
            CreateStaffAvailabilityDTO::class
        );
    }

    /**
     * Display the calendar view for the authenticated staff member.
     */
    public function index(Request $request)
    {
        return parent::index($request);
    }

    /**
     * Handle the request for calendar events.
     */
    public function getEvents(GetEventsRequest $request)
    {
        $validated = $request->validated();

        $staffId = Auth::user()->staff->id;

        // Get personal availability slots
        $availabilities = StaffAvailability::where('staff_id', $staffId)
            ->where('start_time', '>=', $request->start)
            ->where('end_time', '<=', $request->end)
            ->get();

        $availabilityEvents = $availabilities->map(function ($availability) {
            // The model accessors already handle timezone conversion from UTC to local timezone
            // So we can directly use the attributes without additional conversion
            return [
                'id' => $availability->id,
                'title' => $availability->status,
                'start' => $availability->start_time->format('Y-m-d H:i:s'),
                'end' => $availability->end_time->format('Y-m-d H:i:s'),
                'backgroundColor' => $availability->status === 'Available' ? '#28a745' : '#dc3545',
                'borderColor' => $availability->status === 'Available' ? '#28a745' : '#dc3545',
                'extendedProps' => ['status' => $availability->status, 'is_editable' => true],
            ];
        });

        // Get scheduled work assignments (visits)
        $visits = VisitService::where('staff_id', $staffId)
            ->where('scheduled_at', '>=', $request->start)
            ->where('scheduled_at', '<=', $request->end)
            ->where('status', '!=', 'Cancelled')
            ->with('patient')
            ->get();

        $visitEvents = $visits->map(function ($visit) {
            $startTime = Carbon::parse($visit->scheduled_at, 'UTC')->setTimezone(config('app.timezone'));
            $endTime = $startTime->copy()->addHour();

            return [
                'id' => 'visit_'.$visit->id,
                'title' => 'Visit: '.$visit->patient->full_name,
                'start' => $startTime->format('Y-m-d H:i:s'),
                'end' => $endTime->format('Y-m-d H:i:s'),
                'backgroundColor' => '#0284c7',
                'borderColor' => '#0284c7',
                'extendedProps' => ['is_editable' => false],
            ];
        });

        return response()->json($availabilityEvents->concat($visitEvents));
    }
}
