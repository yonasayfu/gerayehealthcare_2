<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Http\Requests\StoreStaffLeaveRequest;

class LeaveRequestController extends Controller
{
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

        Auth::user()->staff->leaveRequests()->create($validated);

        return redirect()->back()->with('success', 'Leave request submitted successfully.');
    }
}