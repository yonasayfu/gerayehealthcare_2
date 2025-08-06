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

    public function update(int $id, array|object $data): EventRecommendation
    {
        $eventRecommendation = parent::update($id, $data);

        if ($data['status'] === 'approved') {
            event(new PatientCreatedFromRecommendation($data['patient_name'], $data['patient_phone'], $eventRecommendation));
            event(new EventParticipantRegistered($data['event_id'], $eventRecommendation->linked_patient_id));
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
