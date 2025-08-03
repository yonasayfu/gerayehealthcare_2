<?php

namespace App\Services;

use App\Models\EventRecommendation;
use App\Models\Patient;
use App\Models\EventParticipant;
use Illuminate\Http\Request;
use App\Http\Traits\ExportableTrait;
use App\Http\Config\AdditionalExportConfigs;

class EventRecommendationService extends BaseService
{
    use ExportableTrait;

    public function __construct(EventRecommendation $eventRecommendation)
    {
        parent::__construct($eventRecommendation);
    }

    protected function applySearch($query, $search)
    {
        $query->where('patient_name', 'ilike', "%{$search}%")
              ->orWhere('source_channel', 'ilike', "%{$search}%");
    }

    public function getAll(Request $request)
    {
        $query = $this->model->query();

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

    public function update(int $id, array $data): EventRecommendation
    {
        $eventRecommendation = parent::update($id, $data);

        if ($data['status'] === 'approved') {
            // Find or create patient
            $patient = Patient::firstOrCreate(
                ['phone_number' => $data['patient_phone']],
                [
                    'full_name' => $data['patient_name'],
                    'email' => null,
                    'date_of_birth' => null,
                    'gender' => null,
                    'address' => null,
                    'emergency_contact' => null,
                    'source' => 'Event Recommendation',
                    'geolocation' => null,
                    'patient_code' => 'PAT-' . str_pad(Patient::count() + 1, 5, '0', STR_PAD_LEFT),
                ]
            );

            // Link the recommendation to the patient
            $eventRecommendation->linked_patient_id = $patient->id;
            $eventRecommendation->save();

            // Create event participant record
            EventParticipant::firstOrCreate(
                [
                    'event_id' => $data['event_id'],
                    'patient_id' => $patient->id,
                ],
                ['status' => 'registered']
            );
        }

        return $eventRecommendation;
    }

    public function export(Request $request)
    {
        return $this->handleExport($request, EventRecommendation::class, AdditionalExportConfigs::getEventRecommendationConfig());
    }

    public function printSingle($id)
    {
        $eventRecommendation = $this->getById($id);
        return $this->handlePrintSingle($eventRecommendation, AdditionalExportConfigs::getEventRecommendationConfig());
    }

    public function printCurrent(Request $request)
    {
        return $this->handlePrintCurrent($request, EventRecommendation::class, AdditionalExportConfigs::getEventRecommendationConfig());
    }

    public function printAll(Request $request)
    {
        return $this->handlePrintAll($request, EventRecommendation::class, AdditionalExportConfigs::getEventRecommendationConfig());
    }
}
