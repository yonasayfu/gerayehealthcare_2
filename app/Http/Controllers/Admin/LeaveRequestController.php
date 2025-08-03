<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Base\BaseController;
use App\Services\LeaveRequestService;
use App\Models\LeaveRequest;
use App\Services\Validation\Rules\LeaveRequestRules;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LeaveRequestController extends BaseController
{
    public function __construct(LeaveRequestService $leaveRequestService)
    {
        parent::__construct(
            $leaveRequestService,
            LeaveRequestRules::class,
            'Admin/LeaveRequests',
            'leaveRequests',
            LeaveRequest::class
        );
    }

    public function update(Request $request, LeaveRequest $leaveRequest)
    {
        try {
            $this->service->update($leaveRequest->id, $request->all());
            session()->flash('success', 'Leave request status updated.');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
        return Inertia::location(route('admin.admin-leave-requests.index', request()->query()));
    }
}
