<?php

namespace App\Services;

use App\DTOs\CreateLeaveRequestDTO;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LeaveRequestService extends BaseService
{
    public function __construct(LeaveRequest $leaveRequest)
    {
        parent::__construct($leaveRequest);
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

        $sortBy = $request->input('sort', 'created_at');
        $sortOrder = $request->input('direction', 'desc');

        if ($sortBy === 'staff_first_name') {
            $query->join('staff', 'leave_requests.staff_id', '=', 'staff.id')
                  ->orderBy('staff.first_name', $sortOrder)
                  ->select('leave_requests.*');
        } else {
            $query->orderBy($sortBy, $sortOrder);
        }

        return $query->paginate($request->input('per_page', 10));
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

        $freshLeaveRequest = $leaveRequest->fresh();

        if ($freshLeaveRequest === null) {
            Log::error('LeaveRequest Update Service: Failed to retrieve fresh instance after update!', [
                'leaveRequestId' => $leaveRequest->id,
                'data_attempted' => $data,
                'message' => 'This means the record was possibly deleted or not saved correctly. Check database and model fillable.'
            ]);
            throw new \Exception('Failed to confirm leave request update.');
        }

        Log::info('LeaveRequest Update Service: Database updated successfully - fresh instance found', [
            'leaveRequestId' => $freshLeaveRequest->id,
            'new_status_in_db_fresh_instance' => $freshLeaveRequest->status,
        ]);

        return $freshLeaveRequest;
    }
}
