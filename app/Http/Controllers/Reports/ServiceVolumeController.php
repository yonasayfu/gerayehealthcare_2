<?php

namespace App\Http\Controllers\Reports;

use App\Enums\RoleEnum;
use App\Http\Traits\ExportableTrait;
use App\Models\ServiceVolumeView;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Http\Controllers\Controller;

class ServiceVolumeController extends Controller
{
    use ExportableTrait;
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'role:'.RoleEnum::SUPER_ADMIN->value.'|'.RoleEnum::ADMIN->value]);
    }

    public function index(Request $request)
    {
        // Initial render; frontend will request data with filters in subsequent iterations
        return Inertia::render('Reports/ServiceVolume', [
            'filters' => [
                'preset' => $request->get('preset', 'this_quarter'),
                'granularity' => $request->get('granularity', 'quarter'),
            ],
        ]);
    }

    public function data(Request $request)
    {
        $query = ServiceVolumeView::query();
        $this->applyFilters($query, $request);
        // For charts/tables we usually want all rows within range
        $data = $query->orderBy('bucket_date')->get();

        return response()->json([
            'data' => $data,
        ]);
    }

    public function export(Request $request)
    {
        return $this->handleExport($request, ServiceVolumeView::class, $this->buildExportConfig());
    }

    // Filters: date range, service_category, is_event_service, granularity
    protected function applyFilters(\Illuminate\Database\Eloquent\Builder $query, Request $request): void
    {
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $granularity = $request->input('granularity'); // quarter by default in view
        $serviceCategory = $request->input('service_category');
        $isEvent = $request->boolean('is_event_service', null);

        if ($dateFrom) {
            $query->whereDate('bucket_date', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('bucket_date', '<=', $dateTo);
        }
        if ($granularity) {
            $query->where('granularity', $granularity);
        }
        if ($serviceCategory) {
            $query->where('service_category', $serviceCategory);
        }
        if (!is_null($isEvent)) {
            $query->where('is_event_service', $isEvent);
        }
    }

    protected function buildExportConfig(): array
    {
        return [
            'filename_prefix' => 'service_volume_report',
            'csv' => [
                'headers' => ['#', 'Bucket', 'Granularity', 'Service Category', 'Total Visits', 'Unique Patients', 'Event Service'],
                'fields' => [
                    'index',
                    'bucket_label',
                    'granularity',
                    ['field' => 'service_category', 'default' => '-'],
                    'total_visits',
                    'unique_patients',
                    [
                        'field' => 'is_event_service',
                        'transform' => fn($v) => $v ? 'Yes' : 'No',
                    ],
                ],
            ],
            'pdf' => [
                'view' => 'pdf.universal-report',
                'document_title' => 'Service Volume Report',
                'filename_prefix' => 'service_volume_report',
                'orientation' => 'landscape',
                'paper' => 'a4',
                'columns' => [
                    ['key' => 'bucket_label', 'label' => 'Bucket'],
                    ['key' => 'granularity', 'label' => 'Granularity'],
                    ['key' => 'service_category', 'label' => 'Service Category'],
                    ['key' => 'total_visits', 'label' => 'Total Visits'],
                    ['key' => 'unique_patients', 'label' => 'Unique Patients'],
                    ['key' => 'is_event_service', 'label' => 'Event Service'],
                ],
            ],
        ];
    }
}
