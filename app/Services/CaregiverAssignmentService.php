<?php

namespace App\Services;

use App\Models\CaregiverAssignment;
use App\Models\Staff;
use Illuminate\Http\Request;
use App\Notifications\NewCaregiverAssignment;
use Illuminate\Support\Facades\Notification;
use App\Http\Traits\ExportableTrait;
use App\Http\Config\ExportConfig;
use App\Events\CaregiverAssigned;

class CaregiverAssignmentService extends BaseService
{
    use ExportableTrait;

    public function __construct(CaregiverAssignment $caregiverAssignment)
    {
        parent::__construct($caregiverAssignment);
    }

    protected function applySearch($query, $search)
    {
        $query->whereHas('patient', fn($q) => $q->where('full_name', 'ilike', "%{$search}%"))
              ->orWhereHas('staff', fn($q) => $q->where('first_name', 'ilike', "%{$search}%"));
    }

    public function getAll(Request $request, array $with = [])
    {
        $query = $this->model->with(array_merge(['patient', 'staff'], $with));

        if ($request->has('search')) {
            $this->applySearch($query, $request->input('search'));
        }

        if ($request->has('sort')) {
            $direction = $request->input('direction', 'asc');
            $query->orderBy($request->input('sort'), $direction);
        }

        return $query->paginate($request->input('per_page', 5));
    }

    public function create(array|object $data): CaregiverAssignment
    {
        $assignment = parent::create($data);

        event(new CaregiverAssigned($assignment));

        return $assignment;
    }

    public function update(int $id, array|object $data)
    {
        $assignment = parent::update($id, $data);

        // Optionally, you can create a different event for updates, e.g., CaregiverAssignmentUpdated
        event(new CaregiverAssigned($assignment));

        return $assignment;
    }

    public function export(Request $request)
    {
        return $this->handleExport($request, CaregiverAssignment::class, ExportConfig::getCaregiverAssignmentConfig());
    }

    public function printAll(Request $request)
    {
        return $this->handlePrintAll($request, CaregiverAssignment::class, ExportConfig::getCaregiverAssignmentConfig());
    }

    public function printCurrent(Request $request)
    {
        return $this->handlePrintCurrent($request, CaregiverAssignment::class, ExportConfig::getCaregiverAssignmentConfig());
    }

    public function printSingle($id)
    {
        $assignment = $this->getById($id);
        $assignment->load(['staff', 'patient']);

        $config = ExportConfig::getCaregiverAssignmentConfig()['single_record'];
        $config['title'] = 'Assignment Record - #' . $assignment->id;
        $config['document_title'] = 'Caregiver Assignment Record';
        $config['filename'] = "assignment-{$assignment->id}.pdf";
        
        return $this->handlePrintSingle($assignment, $config);
    }

   
}
