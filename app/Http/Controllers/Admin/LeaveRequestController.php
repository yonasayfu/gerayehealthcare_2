<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Base\BaseController;
use App\Models\LeaveRequest;
use App\Services\LeaveRequestService;
use App\Services\Validation\Rules\LeaveRequestRules;

class LeaveRequestController extends BaseController
{
    public function __construct(LeaveRequestService $leaveRequestService)
    {
        parent::__construct(
            $leaveRequestService,
            LeaveRequestRules::class,
            'Admin/LeaveRequests',
            'leaveRequests',
            LeaveRequest::class,
            'App\\DTOs\\UpdateLeaveRequestDTO'
        );
    }
}
