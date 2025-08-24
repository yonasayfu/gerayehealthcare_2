<?php

namespace App\Http\Controllers\Reports;

use App\Enums\RoleEnum;
use App\Http\Traits\ExportableTrait;
use App\Models\MarketingRoiView;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Http\Controllers\Controller;

class MarketingRoiController extends Controller
{
    use ExportableTrait;
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'role:'.RoleEnum::SUPER_ADMIN->value.'|'.RoleEnum::ADMIN->value]);
    }

    public function index(Request $request)
    {
        return Inertia::render('Reports/MarketingROI', [
            'filters' => [
                'preset' => $request->get('preset', 'this_quarter'),
                'granularity' => $request->get('granularity', 'quarter'),
            ],
        ]);
    }

    public function data(Request $request)
    {
        $query = MarketingRoiView::query();
        $this->applyFilters($query, $request);
        $data = $query->orderBy('bucket_date')->get();

        return response()->json(['data' => $data]);
    }

    public function export(Request $request)
    {
        return $this->handleExport($request, MarketingRoiView::class, $this->buildExportConfig());
    }

    protected function applyFilters(\Illuminate\Database\Eloquent\Builder $query, Request $request): void
    {
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $granularity = $request->input('granularity');
        $platform = $request->input('platform');

        if ($dateFrom) {
            $query->whereDate('bucket_date', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('bucket_date', '<=', $dateTo);
        }
        if ($granularity) {
            $query->where('granularity', $granularity);
        }
        if ($platform) {
            $query->where('platform', $platform);
        }
    }

    protected function buildExportConfig(): array
    {
        return [
            'filename_prefix' => 'marketing_roi_report',
            'csv' => [
                'headers' => ['#', 'Bucket', 'Platform', 'Impressions', 'Clicks', 'Conversions', 'Revenue', 'Spend', 'ROI %'],
                'fields' => [
                    'index',
                    'bucket_label',
                    ['field' => 'platform', 'default' => '-'],
                    'impressions',
                    'clicks',
                    'conversions',
                    [
                        'field' => 'revenue_generated',
                        'transform' => fn($v) => number_format((float)$v, 2),
                    ],
                    [
                        'field' => 'spend',
                        'transform' => fn($v) => number_format((float)$v, 2),
                    ],
                    [
                        'field' => 'roi_percentage',
                        'transform' => fn($v) => number_format((float)$v, 2),
                    ],
                ],
            ],
            'pdf' => [
                'view' => 'pdf.universal-report',
                'document_title' => 'Marketing ROI Report',
                'filename_prefix' => 'marketing_roi_report',
                'orientation' => 'landscape',
                'paper' => 'a4',
                'columns' => [
                    ['key' => 'bucket_label', 'label' => 'Bucket'],
                    ['key' => 'platform', 'label' => 'Platform'],
                    ['key' => 'impressions', 'label' => 'Impressions'],
                    ['key' => 'clicks', 'label' => 'Clicks'],
                    ['key' => 'conversions', 'label' => 'Conversions'],
                    ['key' => 'revenue_generated', 'label' => 'Revenue'],
                    ['key' => 'spend', 'label' => 'Spend'],
                    ['key' => 'roi_percentage', 'label' => 'ROI %'],
                ],
            ],
        ];
    }
}
