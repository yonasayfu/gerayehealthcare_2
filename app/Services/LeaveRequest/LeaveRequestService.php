<?php

namespace App\Services\LeaveRequest;

use App\Models\LeaveRequest;
use App\Services\Base\BaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Notifications\LeaveRequestStatusUpdated;

class LeaveRequestService extends BaseService
{
    public function __construct(LeaveRequest $leaveRequest)
    {
        parent::__construct($leaveRequest);
    }

    protected function applySearch($query, $search)
    {
        $query->where(function ($q) use ($search) {
            $q->where('reason', 'ilike', '%'.$search.'%')
                ->orWhere('status', 'ilike', '%'.$search.'%')
                ->orWhereHas('staff', function ($sq) use ($search) {
                    $sq->where('first_name', 'ilike', '%'.$search.'%')
                        ->orWhere('last_name', 'ilike', '%'.$search.'%');
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

        if ($sortBy === 'staff_first_name') {
            $query->join('staff', 'leave_requests.staff_id', '=', 'staff.id')
                ->orderBy('staff.first_name', $sortOrder)
                ->select('leave_requests.*');
            } else {
                $query->orderBy($sortBy, $sortOrder);
            }

        $perPage = (int) $request->input('per_page', 10);

        // Ensure pagination links preserve current filters
        $paginator = $query->paginate($perPage);
        $paginator->appends($request->only([
            'search', 'sort', 'direction', 'per_page', 'sort_by', 'sort_order',
        ]));

        return $paginator;
    }

    public function update(int $id, array|object $data): LeaveRequest
    {
        $leaveRequest = $this->getById($id);

        Log::info('LeaveRequest Update Service: Request received', [
            'leaveRequestId' => $leaveRequest->id,
            'status_from_data' => $data['status'],
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
}
