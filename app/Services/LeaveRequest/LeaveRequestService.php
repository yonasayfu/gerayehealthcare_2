<?php

namespace App\Services\LeaveRequest;

use App\Models\LeaveRequest;
use App\Notifications\LeaveRequestStatusUpdated;
use App\Notifications\LeaveRequestSubmitted;
use App\Services\Base\BaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LeaveRequestService extends BaseService
{
    public function __construct()
    {
        parent::__construct(new LeaveRequest());
    }

    protected function applySearch($query, $search)
    {
        $query->where(function ($q) use ($search) {
            $q->where('reason', 'ilike', '%' . $search . '%')
                ->orWhere('status', 'ilike', '%' . $search . '%')
                ->orWhereHas('staff', function ($sq) use ($search) {
                    $sq->where('first_name', 'ilike', '%' . $search . '%')
                        ->orWhere('last_name', 'ilike', '%' . $search . '%');
                });
        });
    }

    public function getAll(Request $request, array $with = [])
    {
        $query = $this->model->with(array_merge(['staff'], $with));

        if ($request->filled('search')) {
            $this->applySearch($query, $request->input('search'));
        }

        // Accept both new and legacy sort keys from the UI
        $sortBy = $request->input('sort', $request->input('sort_by', 'created_at'));
        $sortOrder = $request->input('direction', $request->input('sort_order', 'desc'));

        // Department/Position filters
        $department = $request->input('department');
        $position = $request->input('position');
        $type = $request->input('type');

        $needsJoin = ($sortBy === 'staff_first_name') || !empty($department) || !empty($position);
        if ($needsJoin) {
            $query->join('staff', 'leave_requests.staff_id', '=', 'staff.id')
                ->select('leave_requests.*');

            if (!empty($department)) {
                $query->where('staff.department', 'ilike', '%' . $department . '%');
            }
            if (!empty($position)) {
                $query->where('staff.position', 'ilike', '%' . $position . '%');
            }

            if ($sortBy === 'staff_first_name') {
                $query->orderBy('staff.first_name', $sortOrder);
            } else {
                $query->orderBy($sortBy, $sortOrder);
            }
        } else {
            $query->orderBy($sortBy, $sortOrder);
        }

        if (!empty($type)) {
            $query->where('type', $type);
        }

        $perPage = (int) $request->input('per_page', 10);

        // Ensure pagination links preserve current filters
        $paginator = $query->paginate($perPage);
        $paginator->appends($request->only([
            'search', 'sort', 'direction', 'per_page', 'sort_by', 'sort_order', 'department', 'position', 'type',
        ]));

        return $paginator;
    }

    public function create(array | object $data): LeaveRequest
    {
        Log::info('LeaveRequest Create Service: Request received', [
            'data' => $data,
        ]);

        $leaveRequest = parent::create($data);

        Log::info('LeaveRequest Create Service: Successfully created', [
            'leaveRequestId' => $leaveRequest->id,
        ]);

        // Notify admins about the new leave request
        try {
            $this->notifyAdminsAboutNewRequest($leaveRequest);
        } catch (\Throwable $e) {
            Log::error('Failed to send LeaveRequestSubmitted notification to admins', [
                'leaveRequestId' => $leaveRequest->id ?? null,
                'error' => $e->getMessage(),
            ]);
        }

        return $leaveRequest;
    }

    public function update(int $id, array | object $data): LeaveRequest
    {
        $leaveRequest = $this->getById($id);

        Log::info('LeaveRequest Update Service: Request received', [
            'leaveRequestId' => $leaveRequest->id,
            'status_from_data' => $data['status'] ?? null,
            'admin_notes_from_data' => $data['admin_notes'] ?? null,
        ]);

        $updateResult = $leaveRequest->update($data);

        Log::info('LeaveRequest Update Service: Update method result', [
            'leaveRequestId' => $leaveRequest->id,
            'update_successful' => $updateResult,
        ]);

        $freshLeaveRequest = $leaveRequest->fresh(['staff.user']);

        if ($freshLeaveRequest === null) {
            Log::error('LeaveRequest Update Service: Failed to retrieve fresh instance after update!', [
                'leaveRequestId' => $leaveRequest->id,
                'data_attempted' => $data,
                'message' => 'This means the record was possibly deleted or not saved correctly. Check database and model fillable.',
            ]);
            throw new \Exception('Failed to confirm leave request update.');
        }

        // Notify the staff user about status change (if status provided)
        try {
            if (isset($data['status']) && $freshLeaveRequest->staff && $freshLeaveRequest->staff->user) {
                $freshLeaveRequest->staff->user->notify(new LeaveRequestStatusUpdated($freshLeaveRequest));
            }
        } catch (\Throwable $e) {
            Log::error('Failed to send LeaveRequestStatusUpdated notification', [
                'leaveRequestId' => $freshLeaveRequest->id ?? null,
                'error' => $e->getMessage(),
            ]);
        }

        return $freshLeaveRequest;
    }

    /**
     * Notify admins about a new leave request
     */
    protected function notifyAdminsAboutNewRequest(LeaveRequest $leaveRequest): void
    {
        // Get users with admin roles who should be notified
        $adminRoles = ['Super Admin', 'Admin', 'CEO', 'COO'];
        $admins = \App\Models\User::role($adminRoles)->get();

        Log::info('Notifying admins about new leave request', [
            'leaveRequestId' => $leaveRequest->id,
            'adminCount' => $admins->count(),
        ]);

        foreach ($admins as $admin) {
            try {
                $admin->notify(new LeaveRequestSubmitted($leaveRequest));
            } catch (\Throwable $e) {
                Log::error('Failed to send LeaveRequestSubmitted notification to admin', [
                    'adminId' => $admin->id,
                    'leaveRequestId' => $leaveRequest->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }
}
