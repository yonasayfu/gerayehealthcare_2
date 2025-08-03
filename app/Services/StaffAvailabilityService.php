<?php

namespace App\Services;

use App\Models\StaffAvailability;
use App\Models\Staff;
use Illuminate\Http\Request;

class StaffAvailabilityService extends BaseService
{
    public function __construct(StaffAvailability $staffAvailability)
    {
        parent::__construct($staffAvailability);
    }

    public function getCalendarEvents(Request $request): array
    {
        $query = $this->model->with('staff')
            ->where('start_time', '>=', $request->start)
            ->where('end_time', '<=', $request->end);

        if ($request->filled('staff_id')) {
            $query->where('staff_id', $request->staff_id);
        }

        $availabilities = $query->get();

        return $availabilities->map(function ($availability) {
            return [
                'id' => $availability->id,
                'title' => $availability->staff->first_name . ' (' . $availability->status . ')',
                'start' => $availability->start_time->format('Y-m-d H:i:s'),
                'end' => $availability->end_time->format('Y-m-d H:i:s'),
                'backgroundColor' => $availability->status === 'Available' ? '#28a745' : '#dc3545',
                'borderColor' => $availability->status === 'Available' ? '#28a745' : '#dc3545',
                'extendedProps' => [
                    'staff_name' => $availability->staff->first_name . ' ' . $availability->staff->last_name,
                    'status' => $availability->status,
                ]
            ];
        })->toArray();
    }

    public function getAll(Request $request)
    {
        $query = $this->model->with('staff');

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

    public function create(array $data): StaffAvailability
    {
        $this->checkOverlap($data);
        return parent::create($data);
    }

    public function update(int $id, array $data): StaffAvailability
    {
        $this->checkOverlap($data, $id);
        return parent::update($id, $data);
    }

    protected function checkOverlap(array $data, ?int $id = null): void
    {
        $overlap = StaffAvailability::where('staff_id', $data['staff_id'])
            ->where('end_time', '>', $data['start_time'])
            ->where('start_time', '<', $data['end_time']);

        if ($id) {
            $overlap->where('id', '<>', $id);
        }

        if ($overlap->exists()) {
            throw new \Exception('Conflict: Overlapping availability slot exists.');
        }
    }
}
