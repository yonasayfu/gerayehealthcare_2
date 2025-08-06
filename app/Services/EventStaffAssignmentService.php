<?php

namespace App\Services;

use App\DTOs\CreateEventStaffAssignmentDTO;
use App\Models\EventStaffAssignment;
use Illuminate\Http\Request;
use App\Http\Traits\ExportableTrait;
use App\Http\Config\AdditionalExportConfigs;

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

    public function export(Request $request)
    {
        return $this->handleExport($request, EventStaffAssignment::class, AdditionalExportConfigs::getEventStaffAssignmentConfig());
    }

    public function printSingle($id)
    {
        $eventStaffAssignment = $this->getById($id);
        return $this->handlePrintSingle($eventStaffAssignment, AdditionalExportConfigs::getEventStaffAssignmentConfig());
    }

    public function printCurrent(Request $request)
    {
        return $this->handlePrintCurrent($request, EventStaffAssignment::class, AdditionalExportConfigs::getEventStaffAssignmentConfig());
    }

    public function printAll(Request $request)
    {
        return $this->handlePrintAll($request, EventStaffAssignment::class, AdditionalExportConfigs::getEventStaffAssignmentConfig());
    }
}
