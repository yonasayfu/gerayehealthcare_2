<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Base\BaseController;
use App\Models\LeaveRequest;
use App\Services\LeaveRequest\LeaveRequestService;
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

    /**
     * Override index to expose department/position filters to the view.
     */
    public function index(Request $request)
    {
        $data = $this->service->getAll($request, []);

        return Inertia::render($this->viewName.'/Index', [
            $this->dataVariableName => $data,
            'filters' => $request->only([
                'search', 'sort', 'direction', 'per_page', 'sort_by', 'sort_order', 'department', 'position',
            ]),
        ]);
    }
}
