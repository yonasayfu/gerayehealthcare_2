<?php

namespace App\Http\Controllers\Reports;

use App\Enums\RoleEnum;
use App\Http\Traits\ExportableTrait;
use App\Models\RevenueArView;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Http\Controllers\Controller;

class RevenueARController extends Controller
{
    use ExportableTrait;

    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'role:'.RoleEnum::SUPER_ADMIN->value.'|'.RoleEnum::ADMIN->value]);
    }

    public function index(Request $request)
    {
        return Inertia::render('Reports/RevenueAR', [
            'filters' => [
                'preset' => $request->get('preset', 'this_quarter'),
                'granularity' => $request->get('granularity', 'quarter'),
            ],
        ]);
    }

    public function data(Request $request)
    {
        $cacheKey = 'report:revenue_ar:' . md5(json_encode([
            'from' => $request->input('date_from'),
            'to' => $request->input('date_to'),
            'granularity' => $request->input('granularity'),
        ]));

        $data = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($request) {
            $query = RevenueArView::query();
            $this->applyFilters($query, $request);
            return $query->orderBy('bucket_date')->get();
        });

        return response()->json(['data' => $data]);
    }

    public function export(Request $request)
    {
        return $this->handleExport($request, RevenueArView::class, $this->buildExportConfig());
    }

    protected function applyFilters(\Illuminate\Database\Eloquent\Builder $query, Request $request): void
    {
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $granularity = $request->input('granularity');

        if ($dateFrom) {
            $query->whereDate('bucket_date', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('bucket_date', '<=', $dateTo);
        }
        if ($granularity) {
            $query->where('granularity', $granularity);
        }
    }

    protected function buildExportConfig(): array
    {
        return [
            'filename_prefix' => 'revenue_ar_report',
            'csv' => [
                'headers' => ['#', 'Bucket', 'Invoices', 'Total Billed', 'Total Received', 'AR Outstanding', 'Paid', 'Unpaid'],
                'fields' => [
                    'index',
                    'bucket_label',
                    'invoices_count',
                    [
                        'field' => 'total_billed',
                        'transform' => fn($v) => number_format((float)$v, 2),
                    ],
                    [
                        'field' => 'total_received',
                        'transform' => fn($v) => number_format((float)$v, 2),
                    ],
                    [
                        'field' => 'ar_outstanding',
                        'transform' => fn($v) => number_format((float)$v, 2),
                    ],
                    'paid_invoices',
                    'unpaid_invoices',
                ],
            ],
            'pdf' => [
                'view' => 'pdf.universal-report',
                'document_title' => 'Revenue & Accounts Receivable',
                'filename_prefix' => 'revenue_ar_report',
                'orientation' => 'landscape',
                'paper' => 'a4',
                'columns' => [
                    ['key' => 'bucket_label', 'label' => 'Bucket'],
                    ['key' => 'invoices_count', 'label' => 'Invoices'],
                    ['key' => 'total_billed', 'label' => 'Total Billed'],
                    ['key' => 'total_received', 'label' => 'Total Received'],
                    ['key' => 'ar_outstanding', 'label' => 'AR Outstanding'],
                    ['key' => 'paid_invoices', 'label' => 'Paid'],
                    ['key' => 'unpaid_invoices', 'label' => 'Unpaid'],
                ],
            ],
        ];
    }
}
