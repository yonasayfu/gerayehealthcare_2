<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LeaveRequestController extends Controller
{
    /**
     * Display a listing of all leave requests.
     */
    public function index()
    {
        $leaveRequests = LeaveRequest::with('staff') // Eager load staff details
            ->latest()
            ->paginate(15);

        return Inertia::render('Admin/LeaveRequests/Index', [
            'leaveRequests' => $leaveRequests,
        ]);
    }

    /**
     * Update the status of a specific leave request.
     */
    public function update(Request $request, LeaveRequest $leaveRequest)
    {
        $validated = $request->validate([
            'status' => 'required|in:Approved,Denied',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $leaveRequest->update($validated);

        return redirect()->back()->with('success', 'Leave request status updated.');
    }
}