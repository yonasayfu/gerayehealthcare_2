<?php

namespace App\Services;

use App\Models\VisitService;
use App\Models\Staff;
use App\Models\CaregiverAssignment;
use App\Models\EventStaffAssignment;
use App\Events\StaffAssignedToEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use App\Http\Config\AdditionalExportConfigs;

class VisitServiceService extends BaseService
{

    public function __construct(VisitService $visitService)
    {
        parent::__construct($visitService);
    }

    protected function applySearch($query, $search)
    {
        $query->where(function ($q) use ($search) {
            $q->whereHas('patient', fn($pq) => $pq->where('full_name', 'ilike', "%{$search}%"))
                ->orWhereHas('staff', fn($sq) => $sq->where('first_name', 'ilike', "%{$search}%")->orWhere('last_name', 'ilike', "%{$search}%"));
        });
    }

    public function getAll(Request $request)
    {
        $query = $this->model->with(['patient', 'staff']);

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

    public function create(array|object $data): VisitService
    {
        $data = is_object($data) ? (array) $data : $data;

        $assignment = CaregiverAssignment::where('patient_id', $data['patient_id'])
                                           ->where('staff_id', $data['staff_id'])
                                           ->where('status', 'Assigned')
                                           ->latest('id')
                                           ->first();
        
        $data['assignment_id'] = $assignment?->id;

        $staff = Staff::find($data['staff_id']);
        $data['cost'] = ($staff->hourly_rate ?? 0) * 1;

        if (isset($data['prescription_file'])) {
            $data['prescription_file'] = $data['prescription_file']->store('visits/prescriptions', 'public');
        }
        if (isset($data['vitals_file'])) {
            $data['vitals_file'] = $data['vitals_file']->store('visits/vitals', 'public');
        }

        $visitService = parent::create($data);

        // Trigger event for staff assignment
        if (isset($data['event_id']) && isset($data['staff_id'])) {
            event(new StaffAssignedToEvent($data['event_id'], $data['staff_id']));
        }
        return $visitService;
    }

    public function update(int $id, array $data): VisitService
    {
        $visitService = $this->getById($id);
        $this->checkOverlap($data, $visitService->id);

        $assignment = CaregiverAssignment::where('patient_id', $data['patient_id'])
                                           ->where('staff_id', $data['staff_id'])
                                           ->where('status', 'Assigned')
                                           ->latest('id')
                                           ->first();
        $data['assignment_id'] = $assignment?->id;

        $staff = Staff::find($data['staff_id']);
        $data['cost'] = ($staff->hourly_rate ?? 0) * 1;

        if (isset($data['prescription_file'])) {
            if ($visitService->prescription_file) {
                Storage::disk('public')->delete($visitService->prescription_file);
            }
            $data['prescription_file'] = $data['prescription_file']->store('visits/prescriptions', 'public');
        } else {
            unset($data['prescription_file']);
        }

        if (isset($data['vitals_file'])) {
            if ($visitService->vitals_file) {
                Storage::disk('public')->delete($visitService->vitals_file);
            }
            $data['vitals_file'] = $data['vitals_file']->store('visits/vitals', 'public');
        } else {
            unset($data['vitals_file']);
        }

        return parent::update($id, $data);
    }

    public function delete(int $id): void
    {
        $visitService = $this->getById($id);
        if ($visitService->prescription_file) {
            Storage::disk('public')->delete($visitService->prescription_file);
        }
        if ($visitService->vitals_file) {
            Storage::disk('public')->delete($visitService->vitals_file);
        }
        parent::delete($id);
    }

    protected function checkOverlap(array $data, ?int $id = null): void
    {
        $scheduledAt = Carbon::parse($data['scheduled_at']);
        $visitEndTime = $scheduledAt->copy()->addHour();
        
        $overlap = VisitService::where('staff_id', $data['staff_id'])
            ->where('scheduled_at', '<', $visitEndTime)
            ->where('scheduled_at', '>', $scheduledAt->copy()->subHour())
            ->where('status', '!=', 'Cancelled');

        if ($id) {
            $overlap->where('id', '!=', $id);
        }

        if ($overlap->exists()) {
            throw new \Exception('Conflict: This staff member is already scheduled for another visit at this time.');
        }
    }

    public function export(Request $request)
    {
        return $this->handleExport($request, VisitService::class, AdditionalExportConfigs::getVisitServiceConfig());
    }

    public function printSingle($id, Request $request)
    {
        $visitService = $this->getById($id);
        $visitService->load(['registeredByStaff', 'registeredByCaregiver']);
        
        $config = AdditionalExportConfigs::getVisitServiceConfig();
        
        return $this->handlePrintSingle($request, $visitService, $config);
    }

    public function printCurrent(Request $request)
    {
        return $this->handlePrintCurrent($request, VisitService::class, AdditionalExportConfigs::getVisitServiceConfig());
    }

    public function printAll(Request $request)
    {
        return $this->handlePrintAll($request, VisitService::class, AdditionalExportConfigs::getVisitServiceConfig());
    }
}
