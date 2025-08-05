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
            $csvFileName = $csvConfig['filename_prefix'] . '_' . now()->format('Ymd_His') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $csvFileName . '"',
            ];

            $callback = function() use ($data, $csvConfig) {
                $file = fopen('php://output', 'w');
                fputcsv($file, $csvConfig['headers']);

                foreach ($data as $row) {
                    $csvRow = [];
                    foreach ($csvConfig['fields'] as $field) {
                        // Handle fields that are arrays with 'field' and 'transform'
                        if (is_array($field) && isset($field['field'])) {
                            $value = data_get($row, $field['field']);
                            if (isset($field['transform']) && is_callable($field['transform'])) {
                                $csvRow[] = $field['transform']($value, $row);
                            } else {
                                $csvRow[] = $value;
                            }
                        } else {
                            $csvRow[] = data_get($row, $field);
                        }
                    }
                    fputcsv($file, $csvRow);
                }
                fclose($file);
            };

            return Response::stream($callback, 200, $headers);
        } elseif ($type === 'pdf') {
            $pdfConfig = $config['pdf'];
            $pdf = Pdf::loadView($pdfConfig['view'], [
                'data' => $data,
                'title' => $pdfConfig['document_title'],
                'columns' => $pdfConfig['columns'] ?? [],
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

        $pdf = Pdf::loadView($config['all_records']['view'], [
            'data' => $data,
            'title' => $config['all_records']['document_title'],
            'columns' => $config['all_records']['columns'] ?? [],
            'headerInfo' => [
                'logo' => public_path('images/geraye_logo.jpeg'),
                'clinic_name' => 'Geraye Home Care Services',
                'document_title' => $config['all_records']['document_title'],
            ],
            'footerInfo' => [
                'generated_date' => now()->format('F j, Y, g:i a'),
            ],
        ])->setPaper('a4', 'landscape');

        if ($request->input('preview')) {
            return $pdf->stream($config['all_records']['filename_prefix'] . '_' . now()->format('Ymd_His') . '.pdf');
        } else {
            return $pdf->download($config['all_records']['filename_prefix'] . '_' . now()->format('Ymd_His') . '.pdf');
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

        $data = $query->paginate($request->input('per_page', 5));

        $pdf = Pdf::loadView($config['current_page']['view'], [
            'data' => $data,
            'title' => $config['current_page']['document_title'],
            'columns' => $config['current_page']['columns'] ?? [],
            'headerInfo' => [
                'logo' => public_path('images/geraye_logo.jpeg'),
                'clinic_name' => 'Geraye Home Care Services',
                'document_title' => $config['current_page']['document_title'],
            ],
            'footerInfo' => [
                'generated_date' => now()->format('F j, Y, g:i a'),
            ],
        ])->setPaper('a4', 'portrait');

        if ($request->input('preview')) {
            return $pdf->stream($config['current_page']['filename_prefix'] . '_' . now()->format('Ymd_His') . '.pdf');
        } else {
            return $pdf->download($config['current_page']['filename_prefix'] . '_' . now()->format('Ymd_His') . '.pdf');
        }
    }

    protected function handlePrintSingle(Request $request, $modelInstance, array $config)
    {
        $pdf = Pdf::loadView($config['single_record']['view'], [
            'data' => $modelInstance, 
            'title' => $config['single_record']['document_title'], 
            'columns' => $config['single_record']['columns'] ?? [], 
            'headerInfo' => [
                'logo' => public_path('images/geraye_logo.jpeg'),
                'clinic_name' => 'Geraye Home Care Services',
                'document_title' => $config['single_record']['document_title'],
            ],
            'footerInfo' => [
                'generated_date' => now()->format('F j, Y, g:i a'),
            ],
        ])->setPaper('a4', 'portrait');

        if ($request->input('preview')) {
            return $pdf->stream($config['single_record']['filename_prefix'] . '_' . $modelInstance->id . '.pdf');
        } else {
            return $pdf->download($config['single_record']['filename_prefix'] . '_' . $modelInstance->id . '.pdf');
        }
    }
}