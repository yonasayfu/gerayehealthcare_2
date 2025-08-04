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
                'data' => $data, // Changed to 'data' to match print-layout expectation
                'title' => $pdfConfig['document_title'], // Changed to 'title' to match print-layout expectation
                'columns' => $pdfConfig['columns'] ?? [],
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
            'data' => $data, // Changed to 'data' to match print-layout expectation
            'title' => $config['all_records']['document_title'], // Changed to 'title' to match print-layout expectation
            'columns' => $config['all_records']['columns'] ?? [],
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

        $data = $query->paginate($request->input('per_page', 10));

        $pdf = Pdf::loadView($config['current_page']['view'], [
            'data' => $data, // Changed to 'data' to match print-layout expectation
            'title' => $config['current_page']['document_title'], // Changed to 'title' to match print-layout expectation
            'columns' => $config['current_page']['columns'] ?? [],
        ])->setPaper('a4', 'landscape');

        if ($request->input('preview')) {
            return $pdf->stream($config['current_page']['filename_prefix'] . '_' . now()->format('Ymd_His') . '.pdf');
        } else {
            return $pdf->download($config['current_page']['filename_prefix'] . '_' . now()->format('Ymd_His') . '.pdf');
        }
    }

    protected function handlePrintSingle(Request $request, $modelInstance, array $config)
    {
        $pdf = Pdf::loadView($config['single_record']['view'], [
            'data' => $modelInstance, // Changed to 'data' to match print-layout expectation
            'title' => $config['single_record']['document_title'], // Changed to 'title' to match print-layout expectation
            'columns' => $config['single_record']['columns'] ?? [], // Pass columns if needed for single record
        ])->setPaper('a4', 'portrait');

        if ($request->input('preview')) {
            return $pdf->stream($config['single_record']['filename_prefix'] . '_' . $modelInstance->id . '.pdf');
        } else {
            return $pdf->download($config['single_record']['filename_prefix'] . '_' . $modelInstance->id . '.pdf');
        }
    }

    protected function generateSingleRecordPdf(Request $request, $modelInstance, array $config)
    {
        $pdf = Pdf::loadView($config['view'], [
            'data' => $modelInstance, // Changed to 'data' to match print-layout expectation
            'title' => $config['document_title'], // Changed to 'title' to match print-layout expectation
            'columns' => $config['columns'] ?? [], // Pass columns if needed for single record
        ])->setPaper('a4', 'portrait');

        if ($request->input('preview')) {
            return $pdf->stream($config['filename']);
        } else {
            return $pdf->download($config['filename']);
        }
    }
}
