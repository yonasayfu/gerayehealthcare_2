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
                $q->where('reason', 'ilike', '%' . $search . '%')
                  ->orWhere('status', 'ilike', '%' . $search . '%')
                  ->orWhereHas('staff', function ($sq) use ($search) {
                      $sq->where('first_name', 'ilike', '%' . $search . '%')
                         ->orWhere('last_name', 'ilike', '%' . $search . '%');
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
        Log::info('LeaveRequest Update: Request received from frontend', [
            'leaveRequestId' => $leaveRequest->id,
            'status_from_request' => $request->input('status'),
            'admin_notes_from_request' => $request->input('admin_notes'),
            'request_all' => $request->all(), // Log all request data
            'original_leave_request_status_object' => $leaveRequest->status, // Log original status from model object
        ]);

        $validated = $request->validate([
            'status' => 'required|in: Approved,Denied', // Ensure these are case-sensitive matching
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $updateResult = $leaveRequest->update($validated);

        // Add log to check if update was successful
        Log::info('LeaveRequest Update: Update method result', [
            'leaveRequestId' => $leaveRequest->id,
            'update_successful' => $updateResult, // Will be true/false
            'status_after_update_call_object' => $leaveRequest->status, // Check local object status (might not be fresh from DB)
        ]);


        // Add a check before calling fresh()
        $freshLeaveRequest = $leaveRequest->fresh();

        if ($freshLeaveRequest === null) {
            Log::error('LeaveRequest Update: Failed to retrieve fresh instance after update!', [
                'leaveRequestId' => $leaveRequest->id,
                'status_was_on_object' => $leaveRequest->status, // Log current status of the object before fresh()
                'validated_data_attempted' => $validated,
                'message' => 'This means the record was possibly deleted or not saved correctly. Check database and model fillable.'
            ]);
            // Return an error response if we can't confirm the update
            session()->flash('error', 'Failed to confirm leave request update. Please check server logs.');
            return Inertia::location(route('admin.admin-leave-requests.index', request()->query()));
        }

        // This line will only be reached if $freshLeaveRequest is not null
        Log::info('LeaveRequest Update: Database updated successfully - fresh instance found', [
            'leaveRequestId' => $freshLeaveRequest->id,
            'new_status_in_db_fresh_instance' => $freshLeaveRequest->status, // Fetch fresh status from DB
        ]);

        // Store flash message in session manually before the Inertia response
        session()->flash('success', 'Leave request status updated.');

        // Instruct Inertia to navigate to the index page (which re-fetches props)
        // This handles the page refresh and ensures flash messages are passed
        return Inertia::location(route('admin.admin-leave-requests.index', request()->query()));
    }
}