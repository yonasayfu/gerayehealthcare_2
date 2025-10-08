<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;

class LeaveRequestController extends Controller
{
    /**
     * Get pending leave requests for dashboard widget
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getPendingRequests(Request $request)
    {
        $pendingRequests = LeaveRequest::with('staff')
            ->where('status', 'Pending')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return response()->json($pendingRequests);
    }
}