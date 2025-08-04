<?php

namespace App\Http\Traits;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Response;

trait ExportableTrait
{
    protected function handleExport(Request $request, $modelClass, array $config)
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

        // Generate CSV
        $csvFileName = $config['filename_prefix'] . '_' . now()->format('Ymd_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $csvFileName . '"',
        ];

        $callback = function() use ($data, $config) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $config['csv_headers']);

            foreach ($data as $row) {
                $csvRow = [];
                foreach ($config['csv_fields'] as $field) {
                    $csvRow[] = data_get($row, $field);
                }
                fputcsv($file, $csvRow);
            }
            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
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
            $config['data_variable_name'] => $data,
            'document_title' => $config['all_records']['document_title'],
        ])->setPaper('a4', 'landscape');

        return $pdf->download($config['all_records']['filename_prefix'] . '_' . now()->format('Ymd_His') . '.pdf');
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
            $config['data_variable_name'] => $data,
            'document_title' => $config['current_page']['document_title'],
        ])->setPaper('a4', 'landscape');

        return $pdf->download($config['current_page']['filename_prefix'] . '_' . now()->format('Ymd_His') . '.pdf');
    }

    protected function handlePrintSingle($modelInstance, array $config)
    {
        $pdf = Pdf::loadView($config['single_record']['view'], [
            'item' => $modelInstance,
            'document_title' => $config['single_record']['document_title'],
        ])->setPaper('a4', 'portrait');

        return $pdf->download($config['single_record']['filename_prefix'] . '_' . $modelInstance->id . '.pdf');
    }

    protected function generateSingleRecordPdf($modelInstance, array $config)
    {
        $pdf = Pdf::loadView($config['view'], [
            'item' => $modelInstance,
            'document_title' => $config['document_title'],
        ])->setPaper('a4', 'portrait');

        return $pdf->download($config['filename']);
    }
}
