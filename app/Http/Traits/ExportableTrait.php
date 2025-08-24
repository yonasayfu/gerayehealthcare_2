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

        // Allow per-config query customization for export (e.g., filter rows)
        if ($type === 'csv') {
            $csvConfigForQuery = $config['csv'] ?? [];
            if (isset($csvConfigForQuery['query_callback']) && is_callable($csvConfigForQuery['query_callback'])) {
                // Provide the query and request to the callback for advanced filtering
                call_user_func($csvConfigForQuery['query_callback'], $query, $request);
            }
        }

        $data = $query->get();

        // Eager load relations for CSV export if configured
        $relations = $config['with_relations'] ?? ($config['csv']['with_relations'] ?? null);
        if ($relations && is_array($relations) && method_exists($data, 'load')) {
            $data->load($relations);
        }

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
                foreach ($data as $index => $row) {
                    $line = [];
                    foreach ($csvConfig['fields'] as $field) {
                        // 1) Literal index column support
                        if ($field === 'index') {
                            $line[] = $index + 1;
                            continue;
                        }

                        // 2) Legacy special-case support
                        if ($field === 'staff_full_name') {
                            $staff = $row->staff ?? null;
                            $line[] = $staff ? trim(($staff->first_name ?? '') . ' ' . ($staff->last_name ?? '')) : 'N/A';
                            continue;
                        }

                        // 3) Array field spec with transform/default
                        if (is_array($field)) {
                            $source = $field['field'] ?? null;
                            $value = $source ? data_get($row, $source, null) : null;
                            if (isset($field['transform']) && is_callable($field['transform'])) {
                                $value = call_user_func($field['transform'], $value, $row);
                            }
                            if (($value === null || $value === '') && array_key_exists('default', $field)) {
                                $value = $field['default'];
                            }
                            $line[] = $value ?? '';
                            continue;
                        }

                        // 4) Simple string path (dot-notation supported)
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

            $paper = $pdfConfig['paper'] ?? $pdfConfig['paper_size'] ?? 'a4';
            $orientation = $pdfConfig['orientation'] ?? 'portrait';

            $headerInfo = [
                'logo' => public_path('images/geraye_logo.jpeg'),
                'clinic_name' => 'Geraye Home Care Services',
                'document_title' => $pdfConfig['document_title'],
                'paper_size' => $paper,
            ];
            if (!empty($pdfConfig['header_info']) && is_array($pdfConfig['header_info'])) {
                $headerInfo = array_merge($headerInfo, $pdfConfig['header_info']);
            }

            $footerInfo = [
                'generated_date' => now()->format('F j, Y, g:i a'),
            ];
            if (!empty($pdfConfig['footer_info']) && is_array($pdfConfig['footer_info'])) {
                $footerInfo = array_merge($footerInfo, $pdfConfig['footer_info']);
            }

            $pdf = Pdf::loadView($pdfConfig['view'], [
                'data' => $data,
                'title' => $pdfConfig['document_title'],
                'columns' => $pdfConfig['columns'] ?? [], // Pass columns instead of fields
                'hide_footer' => $pdfConfig['hide_footer'] ?? false,
                'headerInfo' => $headerInfo,
                'footerInfo' => $footerInfo,
            ])->setPaper($paper, $orientation);

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
            'view' => 'pdf-layout',
            'document_title' => 'All Records',
            'filename_prefix' => 'export',
            'orientation' => 'landscape',
            'paper' => 'a4',
        ];

        // Eager load relations for print all
        if (isset($allRecordsConfig['with_relations']) && is_array($allRecordsConfig['with_relations'])) {
            $data->load($allRecordsConfig['with_relations']);
        }

        // Add index to data if include_index is true
        if (($allRecordsConfig['include_index'] ?? false) === true) {
            $data = $data->map(function ($item, $key) {
                $item->index = $key + 1;
                return $item;
            });
        }

        $paper = $allRecordsConfig['paper'] ?? $allRecordsConfig['paper_size'] ?? 'a4';
        $orientation = $allRecordsConfig['orientation'] ?? 'landscape';

        $headerInfo = [
            'logo' => public_path('images/geraye_logo.jpeg'),
            'clinic_name' => 'Geraye Home Care Services',
            'document_title' => $allRecordsConfig['document_title'] ?? 'All Records',
            'paper_size' => $paper,
        ];
        if (!empty($allRecordsConfig['header_info']) && is_array($allRecordsConfig['header_info'])) {
            $headerInfo = array_merge($headerInfo, $allRecordsConfig['header_info']);
        }

        $footerInfo = [
            'generated_date' => now()->format('F j, Y, g:i a'),
        ];
        if (!empty($allRecordsConfig['footer_info']) && is_array($allRecordsConfig['footer_info'])) {
            $footerInfo = array_merge($footerInfo, $allRecordsConfig['footer_info']);
        }

        $pdf = Pdf::loadView($allRecordsConfig['view'], [
            'data' => $data,
            'title' => $allRecordsConfig['document_title'] ?? 'All Records',
            'columns' => $allRecordsConfig['columns'] ?? [], // Pass columns instead of fields
            'hide_footer' => $allRecordsConfig['hide_footer'] ?? false,
            'headerInfo' => $headerInfo,
            'footerInfo' => $footerInfo,
        ])->setPaper($paper, $orientation);

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
            'view' => 'pdf-layout',
            'document_title' => 'Current View',
            'filename_prefix' => 'export-current',
            'orientation' => 'portrait',
            'paper' => 'a4',
        ];

        // Eager load relations for print current
        if (isset($currentPageConfig['with_relations']) && is_array($currentPageConfig['with_relations'])) {
            $query->with($currentPageConfig['with_relations']);
        }

        $data = $query->paginate($request->input('per_page', 5));

        $paper = $currentPageConfig['paper'] ?? $currentPageConfig['paper_size'] ?? 'a4';
        $orientation = $currentPageConfig['orientation'] ?? 'portrait';

        $headerInfo = [
            'logo' => public_path('images/geraye_logo.jpeg'),
            'clinic_name' => 'Geraye Home Care Services',
            'document_title' => $currentPageConfig['document_title'] ?? 'Current View',
            'paper_size' => $paper,
        ];
        if (!empty($currentPageConfig['header_info']) && is_array($currentPageConfig['header_info'])) {
            $headerInfo = array_merge($headerInfo, $currentPageConfig['header_info']);
        }

        $footerInfo = [
            'generated_date' => now()->format('F j, Y, g:i a'),
        ];
        if (!empty($currentPageConfig['footer_info']) && is_array($currentPageConfig['footer_info'])) {
            $footerInfo = array_merge($footerInfo, $currentPageConfig['footer_info']);
        }

        $pdf = Pdf::loadView($currentPageConfig['view'], [
            'data' => $data,
            'title' => $currentPageConfig['document_title'] ?? 'Current View',
            'columns' => $currentPageConfig['columns'] ?? [], // Pass columns instead of fields
            'hide_footer' => $currentPageConfig['hide_footer'] ?? false,
            'headerInfo' => $headerInfo,
            'footerInfo' => $footerInfo,
        ])->setPaper($paper, $orientation);

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
            'view' => 'pdf-layout',
            'document_title' => 'Record Details',
            'filename_prefix' => 'record',
            'paper' => 'a4',
            'orientation' => 'portrait',
        ];

        // Eager load relations for single record
        if (isset($singleRecordConfig['with_relations']) && is_array($singleRecordConfig['with_relations'])) {
            $modelInstance->load($singleRecordConfig['with_relations']);
        }

        $paper = $singleRecordConfig['paper'] ?? $singleRecordConfig['paper_size'] ?? 'a4';
        $orientation = $singleRecordConfig['orientation'] ?? 'portrait';

        $headerInfo = [
            'logo' => public_path('images/geraye_logo.jpeg'),
            'clinic_name' => 'Geraye Home Care Services',
            'document_title' => $singleRecordConfig['document_title'] ?? 'Record Details',
            'paper_size' => $paper,
        ];
        if (!empty($singleRecordConfig['header_info']) && is_array($singleRecordConfig['header_info'])) {
            $headerInfo = array_merge($headerInfo, $singleRecordConfig['header_info']);
        }

        $footerInfo = [
            'generated_date' => now()->format('F j, Y, g:i a'),
        ];
        if (!empty($singleRecordConfig['footer_info']) && is_array($singleRecordConfig['footer_info'])) {
            $footerInfo = array_merge($footerInfo, $singleRecordConfig['footer_info']);
        }

        $pdf = Pdf::loadView($singleRecordConfig['view'], [
            'data' => $modelInstance, 
            'title' => $singleRecordConfig['document_title'] ?? 'Record Details', 
            'columns' => $singleRecordConfig['columns'] ?? [], // Pass columns instead of fields
            'hide_footer' => $singleRecordConfig['hide_footer'] ?? false,
            'headerInfo' => $headerInfo,
            'footerInfo' => $footerInfo,
        ])->setPaper($paper, $orientation);

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