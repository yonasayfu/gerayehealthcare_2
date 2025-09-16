<?php

namespace App\Services;

use App\Models\Staff;
use App\Models\StaffAvailability;
use App\Models\VisitService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class StaffAvailabilityService extends BaseService
{
    public function __construct(StaffAvailability $staffAvailability)
    {
        parent::__construct($staffAvailability);
    }

    public function getCalendarEvents(Request $request): array
    {
        // Include any availability that overlaps the requested window: [start, end)
        $rangeStart = $request->start;
        $rangeEnd = $request->end;
        $query = $this->model->with('staff')
            ->where('end_time', '>', $rangeStart)
            ->where('start_time', '<', $rangeEnd);

        if ($request->filled('staff_id')) {
            $query->where('staff_id', $request->staff_id);
        }

        $availabilities = $query->get();

        return $availabilities->map(function ($availability) {
            return [
                'id' => $availability->id,
                'title' => $availability->staff->first_name.' ('.$availability->status.')',
                'start' => $availability->start_time->format('Y-m-d H:i:s'),
                'end' => $availability->end_time->format('Y-m-d H:i:s'),
                'backgroundColor' => $availability->status === 'Available' ? '#28a745' : '#dc3545',
                'borderColor' => $availability->status === 'Available' ? '#28a745' : '#dc3545',
                'extendedProps' => [
                    'staff_name' => $availability->staff->first_name.' '.$availability->staff->last_name,
                    'status' => $availability->status,
                ],
            ];
        })->toArray();
    }

    public function getAll(Request $request, array $with = [])
    {
        $query = $this->model->with(array_merge(['staff'], $with));

        if ($request->filled('staff_id')) {
            $query->where('staff_id', $request->staff_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('start_date')) {
            $query->whereDate('start_time', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('end_time', '<=', $request->end_date);
        }

        if ($request->filled('sort')) {
            $query->orderBy($request->sort, $request->get('direction', 'asc'));
        } else {
            $query->orderBy('start_time', 'desc');
        }

        return $query->paginate($request->get('per_page', 15));
    }

    public function create(array|object $data): StaffAvailability
    {
        $data = is_object($data) ? (array) $data : $data;
        // Prevent overlapping slot on create as well
        $this->checkOverlap($data, null);

        return parent::create($data);
    }

    public function update(int $id, array|object $data): StaffAvailability
    {
        $data = is_object($data) ? (array) $data : $data;
        $this->checkOverlap($data, $id);

        return parent::update($id, $data);
    }

    protected function checkOverlap(array|object $data, ?int $id = null): void
    {
        $data = is_object($data) ? (array) $data : $data;
        $staffId = (int) ($data['staff_id'] ?? 0);
        $start = $data['start_time'] ?? null;
        $end = $data['end_time'] ?? null;
        $status = (string) ($data['status'] ?? 'Available');

        $overlap = StaffAvailability::where('staff_id', $staffId)
            ->where('end_time', '>', $start)
            ->where('start_time', '<', $end);

        if ($id) {
            $overlap->where('id', '<>', $id);
        }

        if ($overlap->exists()) {
            throw ValidationException::withMessages([
                'error' => 'Conflict: Overlapping availability slot exists for this staff within the selected time range.',
            ]);
        }

        // If marking as Unavailable, ensure no scheduled visits exist in that window
        if (strcasecmp($status, 'Unavailable') === 0) {
            $visitConflict = VisitService::where('staff_id', $staffId)
                ->where('status', '!=', 'Cancelled')
                ->whereBetween('scheduled_at', [$start, $end])
                ->exists();

            if ($visitConflict) {
                throw ValidationException::withMessages([
                    'error' => 'Conflict: Staff has scheduled visits in the selected time range. Cannot set Unavailable over existing assignments.',
                ]);
            }
        }
    }

    /**
     * Return staff records that do NOT have any availability overlapping the given range.
     */
    public function getAvailableStaff(string $start, string $end)
    {
        // Staff who do not have any availability overlapping [start, end)
        $conflictingStaffIds = StaffAvailability::where('end_time', '>', $start)
            ->where('start_time', '<', $end)
            ->pluck('staff_id')
            ->unique()
            ->all();

        return Staff::select('id', 'first_name', 'last_name')
            ->when(! empty($conflictingStaffIds), function ($q) use ($conflictingStaffIds) {
                $q->whereNotIn('id', $conflictingStaffIds);
            })
            ->orderBy('first_name')
            ->get();
    }
}
