<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use Inertia\Inertia; // Ensure this is here

class LeaveRequestController extends Controller
{
    /**
     * Display a listing of all leave requests.
     */
    public function index(Request $request)
    {
        // ... (keep this method exactly as it was) ...
        $search = $request->input('search');
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');

        $validSortColumns = ['created_at', 'start_date', 'end_date', 'status', 'staff_first_name'];
        if (!in_array($sortBy, $validSortColumns)) {
            $sortBy = 'created_at';
        }

        if (!in_array($sortOrder, ['asc', 'desc'])) {
            $sortOrder = 'desc';
        }

        $query = LeaveRequest::query();
        $query->with('staff');

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

        if ($sortBy === 'staff_first_name') {
            $query->join('staff', 'leave_requests.staff_id', '=', 'staff.id')
                  ->orderBy('staff.first_name', $sortOrder)
                  ->select('leave_requests.*');
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

        // Store flash message in session manually before the Inertia response
        session()->flash('success', 'Leave request status updated.');

        // CORRECTED: Removed ->with() from Inertia::location()
        return Inertia::location(route('admin.admin-leave-requests.index', request()->query()));
    }
}