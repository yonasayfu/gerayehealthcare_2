<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use Inertia\Inertia; // Make sure this is imported
use Illuminate\Support\Facades\Log; // Make sure this is imported for logging

class LeaveRequestController extends Controller
{
    /**
     * Display a listing of all leave requests.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');

        // Define valid sortable columns.
        $validSortColumns = ['created_at', 'start_date', 'end_date', 'status', 'staff_first_name'];
        if (!in_array($sortBy, $validSortColumns)) {
            $sortBy = 'created_at';
        }

        if (!in_array($sortOrder, ['asc', 'desc'])) {
            $sortOrder = 'desc';
        }

        // Start building the query
        $query = LeaveRequest::query();

        // Always eager load staff details
        $query->with('staff');

        // Apply search filter
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('reason', 'like', '%' . $search . '%')
                  ->orWhere('status', 'like', '%' . $search . '%')
                  ->orWhereHas('staff', function ($sq) use ($search) {
                      $sq->where('first_name', 'like', '%' . $search . '%')
                         ->orWhere('last_name', 'like', '%' . $search . '%');
                  });
            });
        }

        // Apply sorting based on the requested column
        if ($sortBy === 'staff_first_name') {
            $query->join('staff', 'leave_requests.staff_id', '=', 'staff.id')
                  ->orderBy('staff.first_name', $sortOrder)
                  ->select('leave_requests.*'); // Crucial to select original columns
        } else {
            $query->orderBy($sortBy, $sortOrder);
        }

        $leaveRequests = $query->paginate(15)->withQueryString();

        return Inertia::render('Admin/LeaveRequests/Index', [
            'leaveRequests' => $leaveRequests,
            'filters' => [
                'search' => $search,
                'sort_by' => $sortBy,
                'sort_order' => $sortOrder,
            ],
        ]);
    }

    /**
     * Update the status of a specific leave request.
     */
    public function update(Request $request, LeaveRequest $leaveRequest)
    {
        // Debug log to see what status is received from the frontend
        Log::info('LeaveRequest Update: Request received from frontend', [
            'leaveRequestId' => $leaveRequest->id,
            'status_from_request' => $request->input('status'),
            'admin_notes_from_request' => $request->input('admin_notes'),
            'request_all' => $request->all(), // Log all request data
        ]);

        $validated = $request->validate([
            'status' => 'required|in:Approved,Denied', // Ensure these are case-sensitive matching
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $leaveRequest->update($validated);

        // Debug log to confirm update occurred and new status in DB
        Log::info('LeaveRequest Update: Database updated successfully', [
            'leaveRequestId' => $leaveRequest->id,
            'new_status_in_db' => $leaveRequest->fresh()->status, // Fetch fresh status from DB
        ]);

        // Store flash message in session manually before the Inertia response
        session()->flash('success', 'Leave request status updated.');

        // Instruct Inertia to navigate to the index page (which re-fetches props)
        // This handles the page refresh and ensures flash messages are passed
        return Inertia::location(route('admin.admin-leave-requests.index', request()->query()));
    }
}