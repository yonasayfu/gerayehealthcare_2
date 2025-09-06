<?php

namespace App\Services;

use App\Events\EventParticipantRegistered;
use App\Events\PatientCreatedFromRecommendation;
use App\Http\Config\ExportConfig;
use App\Http\Traits\ExportableTrait;
use App\Models\EventRecommendation;
use Illuminate\Http\Request;

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
        // Select with aliases so frontend keys remain simple and consistent
        $query = $this->model->with($with)
            ->select([
                'id',
                'event_id',
                'patient_name',
                'source_channel as source',
                'recommended_by_name as recommended_by',
                'recommended_by_phone',
                'phone_number as patient_phone',
                'notes',
                'status',
                'created_at',
                'updated_at',
            ]);

        if ($request->has('search')) {
            $this->applySearch($query, $request->input('search'));
        }

        if ($request->has('sort')) {
            $direction = $request->input('direction', 'asc');
            $sort = $request->input('sort');
            $columnMap = [
                'source' => 'source_channel',
                'recommended_by' => 'recommended_by_name',
                'patient_phone' => 'phone_number',
            ];
            $orderByColumn = $columnMap[$sort] ?? $sort;
            $query->orderBy($orderByColumn, $direction);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        return $query->paginate($request->input('per_page', 5));
    }

    public function printCurrent(Request $request)
    {
        return $this->handlePrintCurrent($request, EventRecommendation::class, ExportConfig::getEventRecommendationConfig());
    }

    public function printSingle(Request $request, EventRecommendation $eventRecommendation)
    {
        return $this->handlePrintSingle($request, $eventRecommendation, ExportConfig::getEventRecommendationConfig());
    }

    public function export(Request $request)
    {
        // Export (CSV) is intentionally not supported for Event Recommendations per UI spec
        abort(404, 'Export not supported for Event Recommendations');
    }

    public function update(int $id, array|object $data): EventRecommendation
    {
        $eventRecommendation = parent::update($id, $data);

        if ($data['status'] === 'approved') {
            event(new PatientCreatedFromRecommendation($data['patient_name'], $data['phone_number'] ?? null, $eventRecommendation));
            event(new EventParticipantRegistered($data['event_id'], $eventRecommendation->linked_patient_id));
        }

        return $eventRecommendation;
    }
}
