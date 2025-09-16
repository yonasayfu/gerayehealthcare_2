<?php

namespace App\Services\EventStaffAssignment;

use App\Http\Config\ExportConfig;
use App\Http\Traits\ExportableTrait;
use App\Models\EventStaffAssignment;
use App\Services\Base\BaseService;
use Illuminate\Http\Request;

class EventStaffAssignmentService extends BaseService
{
    use ExportableTrait;

    public function __construct(EventStaffAssignment $eventStaffAssignment)
    {
        parent::__construct($eventStaffAssignment);
    }

    protected function applySearch($query, $search)
    {
        $query->where('role', 'ilike', "%{$search}%");
    }

    public function getAll(Request $request, array $with = [])
    {
        // Always eager load event and staff for listing (names in UI/exports)
        $with = array_unique(array_merge(['event', 'staff'], $with));
        $query = $this->model->with($with);

        if ($request->has('search')) {
            $this->applySearch($query, $request->input('search'));
        }

        if ($request->has('sort')) {
            $direction = $request->input('direction', 'asc');
            $query->orderBy($request->input('sort'), $direction);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        return $query->paginate($request->input('per_page', 10));
    }

    public function getById(int $id, array $with = []): EventStaffAssignment
    {
        $with = array_unique(array_merge(['event', 'staff'], $with));

        return $this->model->with($with)->findOrFail($id);
    }

    public function printAll(Request $request)
    {
        return $this->handlePrintAll($request, EventStaffAssignment::class, ExportConfig::getEventStaffAssignmentConfig());
    }

    public function printCurrent(Request $request)
    {
        return $this->handlePrintCurrent($request, EventStaffAssignment::class, ExportConfig::getEventStaffAssignmentConfig());
    }

    public function printSingle(Request $request, EventStaffAssignment $eventStaffAssignment)
    {
        return $this->handlePrintSingle($request, $eventStaffAssignment, ExportConfig::getEventStaffAssignmentConfig());
    }
}
