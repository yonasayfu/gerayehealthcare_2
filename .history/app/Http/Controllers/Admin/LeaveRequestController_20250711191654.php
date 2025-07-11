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
    public function index(Request $request)
    {
        $search = $request->input('search');
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');

        // Define valid sortable columns. 'staff_name' is a conceptual frontend sort,
        // which maps to 'staff.first_name' or 'staff.last_name' in backend.
        // We'll primarily sort by 'staff.first_name' for 'staff_name' sort.
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
            // To sort by a related model's column, we need to join the table.
            // Ensure you select 'leave_requests.*' to prevent column ambiguity.
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
        $validated = $request->validate([
            'status' => 'required|in:Approved,Denied',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $leaveRequest->update($validated);

        // Returning a redirect()->back() will cause Inertia to re-visit the previous page,
        // which will trigger the Inertia request in the frontend's onSuccess.
        // This is the correct behavior for Inertia's PUT/PATCH requests.
        return redirect()->back()->with('success', 'Leave request status updated.');
    }
}