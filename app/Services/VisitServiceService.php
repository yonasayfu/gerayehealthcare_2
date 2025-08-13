<?php

namespace App\Services;

use App\Models\VisitService;
use App\Models\Staff;
use App\Models\StaffAvailability;
use App\Models\CaregiverAssignment;
use App\Models\EventStaffAssignment;
use App\Events\StaffAssignedToEvent;
use App\Services\InvoiceService;
use App\Http\Traits\ExportableTrait;
use App\Http\Config\AdditionalExportConfigs;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class VisitServiceService extends BaseService
{
    use ExportableTrait;

    protected $invoiceService;

    public function __construct(VisitService $visitService, InvoiceService $invoiceService)
    {
        parent::__construct($visitService);
        $this->invoiceService = $invoiceService;
    }

    protected function applySearch($query, $search)
    {
        $query->where(function ($q) use ($search) {
            $q->whereHas('patient', fn($pq) => $pq->where('full_name', 'ilike', "%{$search}%"))
                ->orWhereHas('staff', fn($sq) => $sq->where('first_name', 'ilike', "%{$search}%")->orWhere('last_name', 'ilike', "%{$search}%"));
        });
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
        } else {
            $query->orderBy('created_at', 'desc');
        }

        return $query->paginate($request->input('per_page', 5));
    }

    public function create(array|object $data): VisitService
    {
        $data = is_object($data) ? (array) $data : $data;

        // Validate staff availability before creating
        if (isset($data['scheduled_at']) && isset($data['staff_id'])) {
            $this->validateStaffAvailability($data['staff_id'], $data['scheduled_at']);
        }

        // Find or create caregiver assignment
        $assignment = CaregiverAssignment::where('patient_id', $data['patient_id'])
                                           ->where('staff_id', $data['staff_id'])
                                           ->where('status', 'Assigned')
                                           ->latest('id')
                                           ->first();
        
        // If no assignment exists, create one
        if (!$assignment) {
            $assignment = CaregiverAssignment::create([
                'patient_id' => $data['patient_id'],
                'staff_id' => $data['staff_id'],
                'status' => 'Assigned',
                'assignment_date' => now(),
            ]);
        }
        
        $data['assignment_id'] = $assignment->id;

        $staff = Staff::find($data['staff_id']);
        $data['cost'] = ($staff->hourly_rate ?? 0) * 1;

        if (isset($data['prescription_file']) && $data['prescription_file'] && is_object($data['prescription_file']) && method_exists($data['prescription_file'], 'store')) {
            $data['prescription_file'] = $data['prescription_file']->store('visits/prescriptions', 'public');
        } else {
            unset($data['prescription_file']);
        }
        
        if (isset($data['vitals_file']) && $data['vitals_file'] && is_object($data['vitals_file']) && method_exists($data['vitals_file'], 'store')) {
            $data['vitals_file'] = $data['vitals_file']->store('visits/vitals', 'public');
        } else {
            unset($data['vitals_file']);
        }

        $visitService = parent::create($data);

        // Trigger event for staff assignment
        if (isset($data['event_id']) && isset($data['staff_id'])) {
            event(new StaffAssignedToEvent($data['event_id'], $data['staff_id']));
        }
        return $visitService;
    }

    public function update(int $id, array|object $data): VisitService
    {
        $visitService = $this->getById($id);
        $data = is_object($data) ? (array) $data : $data;

        // Validate staff availability before updating (excluding current visit)
        if (isset($data['scheduled_at']) && isset($data['staff_id'])) {
            $this->validateStaffAvailability($data['staff_id'], $data['scheduled_at'], $id);
        }

        // Only update assignment and cost if patient or staff are changed
        if (isset($data['patient_id']) && isset($data['staff_id'])) {
            $assignment = CaregiverAssignment::where('patient_id', $data['patient_id'])
                ->where('staff_id', $data['staff_id'])
                ->where('status', 'Assigned')
                ->latest('id')
                ->first();
            
            // If no assignment exists, create one
            if (!$assignment) {
                $assignment = CaregiverAssignment::create([
                    'patient_id' => $data['patient_id'],
                    'staff_id' => $data['staff_id'],
                    'status' => 'Assigned',
                    'assignment_date' => now(),
                ]);
            }
            
            $data['assignment_id'] = $assignment->id;

            $staff = Staff::find($data['staff_id']);
            $data['cost'] = ($staff->hourly_rate ?? 0) * 1;
        }

        if (isset($data['prescription_file']) && $data['prescription_file'] && is_object($data['prescription_file']) && method_exists($data['prescription_file'], 'store')) {
            if ($visitService->prescription_file) {
                Storage::disk('public')->delete($visitService->prescription_file);
            }
            $data['prescription_file'] = $data['prescription_file']->store('visits/prescriptions', 'public');
        } else {
            unset($data['prescription_file']);
        }

        if (isset($data['vitals_file']) && $data['vitals_file'] && is_object($data['vitals_file']) && method_exists($data['vitals_file'], 'store')) {
            if ($visitService->vitals_file) {
                Storage::disk('public')->delete($visitService->vitals_file);
            }
            $data['vitals_file'] = $data['vitals_file']->store('visits/vitals', 'public');
        } else {
            unset($data['vitals_file']);
        }

        $updatedVisitService = parent::update($id, $data);

        // Auto-generate invoice when visit is completed
        $this->handleVisitCompletion($updatedVisitService, $visitService);

        return $updatedVisitService;
    }

    /**
     * Handle visit completion workflow - generate invoice and insurance claim
     */
    protected function handleVisitCompletion(VisitService $updatedVisit, VisitService $originalVisit): void
    {
        // Check if status changed to 'Completed'
        if ($updatedVisit->status === 'Completed' && $originalVisit->status !== 'Completed') {
            try {
                // Create invoice automatically
                $invoice = $this->invoiceService->createFromVisitService($updatedVisit);
                
                Log::info("Invoice {$invoice->invoice_number} created for completed visit {$updatedVisit->id}");
                
            } catch (\Exception $e) {
                Log::error("Failed to create invoice for visit {$updatedVisit->id}: " . $e->getMessage());
                // Don't throw exception to avoid breaking visit update
            }
        }
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

    

    /**
     * Validate staff availability for the scheduled time
     */
    protected function validateStaffAvailability(int $staffId, string $scheduledAt, ?int $excludeVisitId = null): void
    {
        $scheduledDateTime = Carbon::parse($scheduledAt);
        $visitEndTime = $scheduledDateTime->copy()->addHour(); // Assume 1-hour duration

        // Check 1: Staff availability slots (if they marked themselves unavailable)
        $isUnavailable = StaffAvailability::where('staff_id', $staffId)
            ->where('status', 'Unavailable')
            ->where(function ($query) use ($scheduledDateTime, $visitEndTime) {
                $query->where('start_time', '<', $visitEndTime)
                      ->where('end_time', '>', $scheduledDateTime);
            })
            ->exists();

        if ($isUnavailable) {
            throw new \Exception('Staff member is marked as unavailable during this time period.');
        }

        // Check 2: Conflicting visit services
        $conflictingVisit = VisitService::where('staff_id', $staffId)
            ->where('status', '!=', 'Cancelled')
            ->where(function ($query) use ($scheduledDateTime, $visitEndTime) {
                $query->where('scheduled_at', '<', $visitEndTime)
                      ->where('scheduled_at', '>', $scheduledDateTime->copy()->subHour());
            })
            ->when($excludeVisitId, function ($query) use ($excludeVisitId) {
                return $query->where('id', '!=', $excludeVisitId);
            })
            ->first();

        if ($conflictingVisit) {
            $conflictTime = Carbon::parse($conflictingVisit->scheduled_at)->format('M d, Y g:i A');
            throw new \Exception("Staff member is already scheduled for another visit at {$conflictTime}. Please choose a different time or staff member.");
        }

        // Check 3: Reasonable scheduling (not too close to other visits)
        $nearbyVisits = VisitService::where('staff_id', $staffId)
            ->where('status', '!=', 'Cancelled')
            ->where(function ($query) use ($scheduledDateTime) {
                $query->whereBetween('scheduled_at', [
                    $scheduledDateTime->copy()->subHours(2),
                    $scheduledDateTime->copy()->addHours(2)
                ]);
            })
            ->when($excludeVisitId, function ($query) use ($excludeVisitId) {
                return $query->where('id', '!=', $excludeVisitId);
            })
            ->count();

        if ($nearbyVisits >= 2) {
            throw new \Exception('Staff member has multiple visits scheduled near this time. Consider scheduling with more time between visits for travel and preparation.');
        }
    }

    public function getById(int $id): VisitService
    {
        return $this->model->with([
            'patient',
            'staff',
        ])->findOrFail($id);
    }

    


}
