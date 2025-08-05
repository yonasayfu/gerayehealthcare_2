<?php

namespace App\Http\Traits;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Response;

trait ExportableTrait
{
    protected function handleExport(Request $request, $modelClass, array $config)
    {
        $type = $request->input('type', 'csv'); // Default to CSV

        $query = $modelClass::query();

        // Apply filters, search, and sorting based on request
        if (method_exists($this, 'applyFilters')) {
            $this->applyFilters($query, $request);
        }
        if (method_exists($this, 'applySearch')) {
            $this->applySearch($query, $request->input('search'));
        }
        if (method_exists($this, 'applySorting')) {
            $this->applySorting($query, $request);
        }

        $data = $query->get();

        if ($type === 'csv') {
            $csvConfig = $config['csv'];
if (!isset($csvConfig['headers']) || !isset($csvConfig['fields'])) {
    abort(500, "CSV export configuration must include 'headers' and 'fields'.");
}

$filename = ($csvConfig['filename_prefix'] ?? ($config['filename_prefix'] ?? 'export')) . '_' . now()->format('Ymd_His') . '.csv';

$headers = [
    'Content-Type' => 'text/csv; charset=UTF-8',
    'Content-Disposition' => "attachment; filename=\"$filename\"",
];

$callback = function () use ($csvConfig, $data) {
    $output = fopen('php://output', 'w');
    // Add UTF-8 BOM for Excel compatibility
    fwrite($output, "\xEF\xBB\xBF");
    fputcsv($output, $csvConfig['headers']);
    foreach ($data as $row) {
        $line = [];
        foreach ($csvConfig['fields'] as $field) {
            $line[] = data_get($row, $field, '');
        }
        fputcsv($output, $line);
    }
    fclose($output);
};

return Response::stream($callback, 200, $headers);
        } elseif ($type === 'pdf') {
            $pdfConfig = $config['pdf'];
            if (!isset($pdfConfig['view'])) {
                abort(500, "PDF export configuration is missing the 'view' key.");
            }

            // Eager load relations for PDF export
            if (isset($pdfConfig['with_relations']) && is_array($pdfConfig['with_relations'])) {
                $data->load($pdfConfig['with_relations']);
            }

            $pdf = Pdf::loadView($pdfConfig['view'], [
                'data' => $data,
                'title' => $pdfConfig['document_title'],
                'columns' => $pdfConfig['columns'] ?? [], // Pass columns instead of fields
                'headerInfo' => [
                    'logo' => public_path('images/geraye_logo.jpeg'),
                    'clinic_name' => 'Geraye Home Care Services',
                    'document_title' => $pdfConfig['document_title'],
                ],
                'footerInfo' => [
                    'generated_date' => now()->format('F j, Y, g:i a'),
                ],
            ])->setPaper('a4', $pdfConfig['orientation'] ?? 'portrait');

            if ($request->input('preview')) {
                return $pdf->stream($pdfConfig['filename_prefix'] . '_' . now()->format('Ymd_His') . '.pdf');
            } else {
                return $pdf->download($pdfConfig['filename_prefix'] . '_' . now()->format('Ymd_His') . '.pdf');
            }
        }

        // If type is not recognized, return a 404 or an error response
        abort(404, 'Export type not supported.');
    }

    protected function handlePrintAll(Request $request, $modelClass, array $config)
    {
        $query = $modelClass::query();

        // Apply filters, search, and sorting based on request
        if (method_exists($this, 'applyFilters')) {
            $this->applyFilters($query, $request);
        }
        if (method_exists($this, 'applySearch')) {
            $this->applySearch($query, $request->input('search'));
        }
        if (method_exists($this, 'applySorting')) {
            $this->applySorting($query, $request);
        }

        $data = $query->get();

        // Check if all_records config exists, otherwise use default
        $allRecordsConfig = $config['all_records'] ?? $config['pdf'] ?? [
            'view' => 'pdf-layout', // Changed from 'print-layout' to 'pdf-layout'
            'document_title' => 'All Records',
            'filename_prefix' => 'export',
            'orientation' => 'landscape'
        ];

        // Eager load relations for print all
        if (isset($allRecordsConfig['with_relations']) && is_array($allRecordsConfig['with_relations'])) {
            $data->load($allRecordsConfig['with_relations']);
        }

        $pdf = Pdf::loadView($allRecordsConfig['view'], [
            'data' => $data,
            'title' => $allRecordsConfig['document_title'] ?? 'All Records',
            'columns' => $allRecordsConfig['columns'] ?? [], // Pass columns instead of fields
            'headerInfo' => [
                'logo' => public_path('images/geraye_logo.jpeg'),
                'clinic_name' => 'Geraye Home Care Services',
                'document_title' => $allRecordsConfig['document_title'] ?? 'All Records',
            ],
            'footerInfo' => [
                'generated_date' => now()->format('F j, Y, g:i a'),
            ],
        ])->setPaper('a4', $allRecordsConfig['orientation'] ?? 'landscape');

        if ($request->input('preview')) {
            return $pdf->stream($allRecordsConfig['filename_prefix'] . '_' . now()->format('Ymd_His') . '.pdf');
        } else {
            return $pdf->download($allRecordsConfig['filename_prefix'] . '_' . now()->format('Ymd_His') . '.pdf');
        }
    }

    protected function handlePrintCurrent(Request $request, $modelClass, array $config)
    {
        $query = $modelClass::query();

        // Apply filters, search, and sorting based on request
        if (method_exists($this, 'applyFilters')) {
            $this->applyFilters($query, $request);
        }
        if (method_exists($this, 'applySearch')) {
            $this->applySearch($query, $request->input('search'));
        }
        if (method_exists($this, 'applySorting')) {
            $this->applySorting($query, $request);
        }

        // Check if current_page config exists, otherwise use default
        $currentPageConfig = $config['current_page'] ?? $config['pdf'] ?? [
            'view' => 'pdf-layout', // Changed from 'print-layout' to 'pdf-layout'
            'document_title' => 'Current View',
            'filename_prefix' => 'export-current',
            'orientation' => 'portrait'
        ];

        // Eager load relations for print current
        if (isset($currentPageConfig['with_relations']) && is_array($currentPageConfig['with_relations'])) {
            $query->with($currentPageConfig['with_relations']);
        }

        $data = $query->paginate($request->input('per_page', 5));

        $pdf = Pdf::loadView($currentPageConfig['view'], [
            'data' => $data,
            'title' => $currentPageConfig['document_title'] ?? 'Current View',
            'columns' => $currentPageConfig['columns'] ?? [], // Pass columns instead of fields
            'headerInfo' => [
                'logo' => public_path('images/geraye_logo.jpeg'),
                'clinic_name' => 'Geraye Home Care Services',
                'document_title' => $currentPageConfig['document_title'] ?? 'Current View',
            ],
            'footerInfo' => [
                'generated_date' => now()->format('F j, Y, g:i a'),
            ],
        ])->setPaper('a4', $currentPageConfig['orientation'] ?? 'portrait');

        if ($request->input('preview')) {
            return $pdf->stream($currentPageConfig['filename_prefix'] . '_' . now()->format('Ymd_His') . '.pdf');
        } else {
            return $pdf->download($currentPageConfig['filename_prefix'] . '_' . now()->format('Ymd_His') . '.pdf');
        }
    }

    protected function handlePrintSingle(Request $request, $modelInstance, array $config)
    {
        // Check if single_record config exists, otherwise use default
        $singleRecordConfig = $config['single_record'] ?? $config['pdf'] ?? [
            'view' => 'pdf-layout', // Changed from 'print-layout' to 'pdf-layout'
            'document_title' => 'Record Details',
            'filename_prefix' => 'record'
        ];

        // Eager load relations for single record
        if (isset($singleRecordConfig['with_relations']) && is_array($singleRecordConfig['with_relations'])) {
            $modelInstance->load($singleRecordConfig['with_relations']);
        }

        $pdf = Pdf::loadView($singleRecordConfig['view'], [
            'data' => $modelInstance, 
            'title' => $singleRecordConfig['document_title'] ?? 'Record Details', 
            'columns' => $singleRecordConfig['columns'] ?? [], // Pass columns instead of fields
            'headerInfo' => [
                'logo' => public_path('images/geraye_logo.jpeg'),
                'clinic_name' => 'Geraye Home Care Services',
                'document_title' => $singleRecordConfig['document_title'] ?? 'Record Details',
            ],
            'footerInfo' => [
                'generated_date' => now()->format('F j, Y, g:i a'),
            ],
        ])->setPaper('a4', 'portrait');

        if ($request->input('preview')) {
            return $pdf->stream($singleRecordConfig['filename_prefix'] . '_' . $modelInstance->id . '.pdf');
        } else {
            return $pdf->download($singleRecordConfig['filename_prefix'] . '_' . $modelInstance->id . '.pdf');
        }
    }

    protected function debugData($data, $config, $type = 'pdf')
    {
        \Log::info("Export Debug - Type: {$type}", [
            'data_count' => is_array($data) ? count($data) : (is_object($data) ? 'object' : 'unknown'),
            'data_type' => gettype($data),
            'config_keys' => array_keys($config),
            'columns' => $config[$type]['columns'] ?? 'no columns',
            'view' => $config[$type]['view'] ?? 'no view',
        ]);
    }
}