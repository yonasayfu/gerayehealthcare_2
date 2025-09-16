<?php

namespace App\Services\EventRecommendation;

use App\Events\EventParticipantRegistered;
use App\Events\PatientCreatedFromRecommendation;
use App\Http\Config\ExportConfig;
use App\Http\Traits\ExportableTrait;
use App\Models\EventRecommendation;
use App\Services\Base\BaseService;
use App\Services\EligibilityRuleEvaluator\EligibilityRuleEvaluator;
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

        $paginator = $query->paginate($request->input('per_page', 5))->withQueryString();

        // Append computed eligibility info for each item (non-blocking)
        $collection = $paginator->getCollection()->transform(function ($item) {
            try {
                $item->eligibility = $this->computeEligibilityForRecommendation($item);
            } catch (\Throwable $e) {
                $item->eligibility = [
                    'status' => 'unknown',
                    'isEligible' => null,
                    'failed' => [],
                    'missing' => [],
                    'checked' => 0,
                    'total' => 0,
                ];
            }

            return $item;
        });
        $paginator->setCollection($collection);

        return $paginator;
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

    /**
     * Compute eligibility for a recommendation using event's criteria.
     * We build a lightweight context from available fields. Unknown fields are treated as missing.
     */
    protected function computeEligibilityForRecommendation($recommendation): array
    {
        $context = [
            // Map some obvious fields to snake_case keys that criteria might use
            'patient_name' => $recommendation->patient_name ?? null,
            'patient_phone' => $recommendation->patient_phone ?? null,
            'source' => $recommendation->source ?? null,
            'recommended_by' => $recommendation->recommended_by ?? null,
            'status' => $recommendation->status ?? null,
        ];

        /** @var \App\Services\EligibilityRuleEvaluator\EligibilityRuleEvaluator $evaluator */
        $evaluator = app(EligibilityRuleEvaluator::class);

        return $evaluator->evaluateForEvent((int) $recommendation->event_id, $context);
    }

    /**
     * Include eligibility on single fetch as well.
     */
    public function getById(int $id, array $with = [])
    {
        $model = parent::getById($id, $with);

        try {
            $model->eligibility = $this->computeEligibilityForRecommendation($model);
        } catch (\Throwable $e) {
            $model->eligibility = [
                'status' => 'unknown',
                'isEligible' => null,
                'failed' => [],
                'missing' => [],
                'checked' => 0,
                'total' => 0,
            ];
        }

        return $model;
    }
}
