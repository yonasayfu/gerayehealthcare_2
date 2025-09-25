<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Base\BaseController;
use App\Models\LeaveRequest;
use App\Services\LeaveRequest\LeaveRequestService;
use App\Services\Validation\Rules\LeaveRequestRules;

class LeaveRequestController extends BaseController
{
    public function __construct(LeaveRequestService $leaveRequestService)
    {
        parent::__construct(
            $leaveRequestService,
            LeaveRequestRules::class,
            'Admin/LeaveRequests',
            'leave-requests', // This should match the resource route name
            LeaveRequest::class,
            'App\\DTOs\\UpdateLeaveRequestDTO'
        );
    }

    // Override the route name method to ensure correct route naming
    // The route in web.php is registered within the admin group which adds the 'admin.' prefix
    // So the full route name will be 'admin.leave-requests'
    protected function getRouteName()
    {
        return 'leave-requests';
    }
}
