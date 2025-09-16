<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Http\Requests\VisitService\CheckInRequest as WebCheckInRequest;
use App\Http\Requests\VisitService\CheckOutRequest as WebCheckOutRequest;
use App\Http\Requests\StoreVisitReportRequest;
use App\Models\Service;
use App\Models\VisitService;
use App\Services\VisitService\VisitServiceService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class MyVisitController extends Controller
{
    protected $visitServiceService;

    public function __construct(VisitServiceService $visitServiceService)
    {
        $this->visitServiceService = $visitServiceService;
    }

    /**
     * Display a listing of the visits assigned to the authenticated staff member.
     */
    public function index()
    {
        $staff = Auth::user()->staff;

        if (! $staff) {
            return redirect()->route('dashboard')->with('error', 'You do not have a staff profile.');
        }

        $visits = VisitService::with('patient')
            ->where('staff_id', $staff->id)
            ->orderBy('scheduled_at', 'desc')
            ->paginate(10);

        return Inertia::render('Staff/MyVisits/Index', [
            'visits' => $visits,
        ]);
    }

    /**
     * Handle the check-in process for a visit.
     */
    public function checkIn(WebCheckInRequest $request, VisitService $visit)
    {
        if ($visit->staff_id !== Auth::user()->staff->id) {
            abort(403);
        }

        $validated = $request->validated();

        $visit->update([
            'check_in_time' => Carbon::now(),
            'check_in_latitude' => $validated['latitude'],
            'check_in_longitude' => $validated['longitude'],
            'status' => 'In Progress',
        ]);

        return back()->with('banner', 'Checked in successfully.')->with('bannerStyle', 'success');
    }

    /**
     * Handle the check-out process for a visit.
     */
    public function checkOut(WebCheckOutRequest $request, VisitService $visit)
    {
        if ($visit->staff_id !== Auth::user()->staff->id) {
            abort(403);
        }

        $validated = $request->validated();

        $now = Carbon::now();
        $checkIn = $visit->check_in_time ? Carbon::parse($visit->check_in_time) : $now;
        $durationHours = max(0, $checkIn->floatDiffInRealHours($now));
        $hourlyRate = optional(Auth::user()->staff)->hourly_rate ?? 0;
        $earned = round($durationHours * (float) $hourlyRate, 2);

        $visit->update([
            'check_out_time' => $now,
            'check_out_latitude' => $validated['latitude'],
            'check_out_longitude' => $validated['longitude'],
            'status' => 'Completed',
            // Set cost based on actual duration at checkout time
            'cost' => $earned,
        ]);

        return back()->with('banner', 'Checked out successfully.')->with('bannerStyle', 'success');
    }

    /**
     * Show the form for filing a post-visit report.
     */
    public function showReportForm(VisitService $visit)
    {
        // Security check
        if ($visit->staff_id !== Auth::user()->staff->id) {
            abort(403);
        }

        return Inertia::render('Staff/MyVisits/FileReport', [
            'visit' => $visit->load('patient'),
            'services' => Service::where('is_active', true)->orderBy('name')->get(['id', 'name', 'price']),
        ]);
    }

    /**
     * Store the post-visit report.
     */
    public function storeReport(StoreVisitReportRequest $request, VisitService $visit)
    {
        // Security check
        if ($visit->staff_id !== Auth::user()->staff->id) {
            abort(403);
        }

        $validated = $request->validated();

        try {
            $dto = new UpdateVisitServiceDTO(
                patient_id: $visit->patient_id,
                staff_id: $visit->staff_id,
                scheduled_at: $visit->scheduled_at->toDateTimeString(),
                check_in_time: $visit->check_in_time?->toDateTimeString(),
                check_out_time: $visit->check_out_time?->toDateTimeString(),
                visit_notes: $validated['visit_notes'],
                prescription_file: $request->file('prescription_file'),
                vitals_file: $request->file('vitals_file'),
                status: $visit->status,
                cost: $validated['cost'],
                is_paid_to_staff: $visit->is_paid_to_staff,
                is_invoiced: $visit->is_invoiced,
                service_id: $validated['service_id'],
                assignment_id: $visit->assignment_id,
                event_id: $visit->event_id
            );
            $this->visitServiceService->update($visit->id, $dto);

            return redirect()->route('staff.my-visits.index')->with('banner', 'Visit report filed successfully.')->with('bannerStyle', 'success');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput()->with('banner', $e->getMessage())->with('bannerStyle', 'danger');
        }
    }
}
