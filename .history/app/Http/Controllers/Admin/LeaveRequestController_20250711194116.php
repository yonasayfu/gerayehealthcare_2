<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;

class LeaveRequestController extends Controller
{
    // ... (index method remains the same) ...

    /**
     * Update the status of a specific leave request.
     */
    public function update(Request $request, LeaveRequest $leaveRequest)
    {
        Log::info('LeaveRequest Update: Request received from frontend', [
            'leaveRequestId' => $leaveRequest->id,
            'status_from_request' => $request->input('status'),
            'admin_notes_from_request' => $request->input('admin_notes'),
            'request_all' => $request->all(),
            'original_leave_request_status' => $leaveRequest->status, // Log original status
        ]);

        $validated = $request->validate([
            'status' => 'required|in:Approved,Denied',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $updateResult = $leaveRequest->update($validated);

        // Add log to check if update was successful
        Log::info('LeaveRequest Update: Update method result', [
            'leaveRequestId' => $leaveRequest->id,
            'update_successful' => $updateResult, // Will be true/false
            'status_after_update_call' => $leaveRequest->status, // Check local object status
        ]);


        // Add a check before calling fresh()
        $freshLeaveRequest = $leaveRequest->fresh();

        if ($freshLeaveRequest === null) {
            Log::error('LeaveRequest Update: Failed to retrieve fresh instance after update!', [
                'leaveRequestId' => $leaveRequest->id,
                'status_was' => $leaveRequest->status, // Log current status of the object
                'validated_data_attempted' => $validated,
                'message' => 'This means the record was possibly deleted or not saved correctly.'
            ]);
            // You might want to return an error response here or re-fetch differently
            return Inertia::location(route('admin.admin-leave-requests.index', request()->query()))
                          ->with('error', 'Failed to confirm leave request update. Please check logs.');
        }

        // Line 94 will now be safer, as it's inside an if-block or uses the checked variable
        Log::info('LeaveRequest Update: Database updated successfully', [
            'leaveRequestId' => $freshLeaveRequest->id,
            'new_status_in_db' => $freshLeaveRequest->status, // Fetch fresh status from DB
        ]);

        session()->flash('success', 'Leave request status updated.');

        return Inertia::location(route('admin.admin-leave-requests.index', request()->query()));
    }
}