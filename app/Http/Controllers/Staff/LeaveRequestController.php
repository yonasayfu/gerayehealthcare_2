<?php

namespace App\Http\Controllers\Staff;

use App\DTOs\CreateLeaveRequestDTO;
use App\Services\LeaveRequestService;
use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Http\Requests\StoreStaffLeaveRequest;

class LeaveRequestController extends Controller
{
    protected $leaveRequestService;

    public function __construct(LeaveRequestService $leaveRequestService)
    {
        $this->leaveRequestService = $leaveRequestService;
    }

    /**
     * Display a listing of the authenticated staff's leave requests.
     */
    public function index()
    {
        $leaveRequests = LeaveRequest::where('staff_id', Auth::user()->staff->id)
            ->latest()
            ->paginate(10);

        return Inertia::render('Staff/MyLeaveRequests/Index', [
            'leaveRequests' => $leaveRequests,
        ]);
    }

    /**
     * Store a newly created leave request in storage.
     */
    public function store(StoreStaffLeaveRequest $request)
    {
        $validated = $request->validated();
        $dto = new CreateLeaveRequestDTO(
            staff_id: Auth::user()->staff->id,
            start_date: $validated['start_date'],
            end_date: $validated['end_date'],
            reason: $validated['reason'],
            status: 'Pending',
            admin_notes: null
        );

        $this->leaveRequestService->create($dto);

        return redirect()->back()->with('banner', 'Leave request submitted successfully.')->with('bannerStyle', 'success');
    }
}