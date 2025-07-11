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
        // Get search and sort parameters from the request
        $search = $request->input('search');
        $sortBy = $request->input('sort_by', 'created_at'); // Default sort by creation date
        $sortOrder = $request->input('sort_order', 'desc'); // Default sort order descending

        // Validate sort_by to prevent SQL injection
        $validSortColumns = ['created_at', 'start_date', 'end_date', 'status'];
        if (!in_array($sortBy, $validSortColumns)) {
            $sortBy = 'created_at';
        }

        // Validate sort_order
        if (!in_array($sortOrder, ['asc', 'desc'])) {
            $sortOrder = 'desc';
        }

        $leaveRequests = LeaveRequest::with('staff') // Eager load staff details
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('reason', 'like', '%' . $search . '%')
                      ->orWhere('status', 'like', '%' . $search . '%')
                      // Search by staff name (first_name or last_name)
                      ->orWhereHas('staff', function ($sq) use ($search) {
                          $sq->where('first_name', 'like', '%' . $search . '%')
                             ->orWhere('last_name', 'like', '%' . $search . '%');
                      });
                });
            })
            ->orderBy($sortBy, $sortOrder)
            ->paginate(15)
            ->withQueryString(); // Keep query string parameters in pagination links

        return Inertia::render('Admin/LeaveRequests/Index', [
            'leaveRequests' => $leaveRequests,
            // Pass current search and sort states to the frontend
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

        // Instead of redirecting back, you can return a response that Inertia can use
        // For example, a JSON response with a success message, which the frontend handles.
        // However, for Inertia's default PUT/PATCH handling, a redirect back
        // will trigger a full Inertia visit to the previous page, which reloads props.
        // Given your manual update in the frontend, `redirect()->back()` is fine.
        return redirect()->back()->with('success', 'Leave request status updated.');
    }
}