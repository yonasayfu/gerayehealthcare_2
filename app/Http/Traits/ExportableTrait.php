<?php

namespace App\Http\Traits;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

trait ExportableTrait
{
    /**
     * Generate CSV export for a collection of models
     */
    protected function generateCsvExport(Collection $models, array $csvConfig): \Illuminate\Http\Response
    {
        $headers = $csvConfig['headers'] ?? [];
        $fields = $csvConfig['fields'] ?? [];
        $filename = $csvConfig['filename'] ?? 'export.csv';

        // Build CSV header row
        $csvData = implode(',', array_map(fn($header) => '"' . $header . '"', $headers)) . "\n";

        // Build CSV data rows
        foreach ($models as $model) {
            $row = [];
            foreach ($fields as $field) {
                $value = $this->getFieldValue($model, $field);
                $row[] = '"' . str_replace('"', '""', $value ?? '') . '"';
            }
            $csvData .= implode(',', $row) . "\n";
        }

        return Response::make($csvData, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Generate PDF export for a collection of models
     */
    protected function generatePdfExport(Collection $models, array $pdfConfig): \Illuminate\Http\Response
    {
        $data = $models->map(function($model, $index) use ($pdfConfig) {
            $row = [];
            
            // Add index if specified
            if ($pdfConfig['include_index'] ?? false) {
                $row['index'] = $index + 1;
            }

            // Map model fields to row data
            foreach ($pdfConfig['fields'] as $key => $field) {
                $row[$key] = $this->getFieldValue($model, $field);
            }

            return $row;
        })->toArray();

        $columns = $pdfConfig['columns'];
        $title = $pdfConfig['title'];
        $documentTitle = $pdfConfig['document_title'] ?? $title;
        $orientation = $pdfConfig['orientation'] ?? 'landscape';
        $filename = $pdfConfig['filename'] ?? 'export.pdf';

        $pdf = Pdf::loadView('print-layout', compact('title', 'data', 'columns', 'documentTitle'))
                    ->setPaper('a4', $orientation);
        
        return $pdf->stream($filename);
    }

    /**
     * Generate single record PDF print
     */
    protected function generateSingleRecordPdf(Model $model, array $config): \Illuminate\Http\Response
    {
        $data = [];
        
        foreach ($config['fields'] as $label => $field) {
            $data[] = [
                'label' => $label,
                'value' => $this->getFieldValue($model, $field)
            ];
        }

        $columns = [
            ['key' => 'label', 'label' => 'Field', 'printWidth' => '30%'],
            ['key' => 'value', 'label' => 'Value', 'printWidth' => '70%'],
        ];

        $title = $config['title'];
        $documentTitle = $config['document_title'] ?? $title;
        $filename = $config['filename'] ?? 'record.pdf';

        $pdf = Pdf::loadView('print-layout', compact('title', 'data', 'columns', 'documentTitle'))
                    ->setPaper('a4', 'portrait');
        
        return $pdf->stream($filename);
    }

    /**
     * Apply common search and sort filters to a query
     */
    protected function applySearchAndSort(Builder $query, Request $request, array $config): Builder
    {
        // Apply search filters
        if ($request->filled('search') && isset($config['searchable_fields'])) {
            $search = $request->input('search');
            $query->where(function($q) use ($search, $config) {
                foreach ($config['searchable_fields'] as $field) {
                    $q->orWhere($field, 'ilike', "%{$search}%");
                }
            });
        }

        // Apply sorting
        if ($request->filled('sort') && !empty($request->input('sort'))) {
            $sortField = $request->input('sort');
            $sortDirection = $request->input('direction', 'asc');
            
            $sortableFields = $config['sortable_fields'] ?? [];
            if (in_array($sortField, $sortableFields)) {
                $query->orderBy($sortField, $sortDirection);
            } else {
                $query->orderBy($config['default_sort'] ?? 'created_at', 'desc');
            }
        } else {
            $query->orderBy($config['default_sort'] ?? 'created_at', 'desc');
        }

        return $query;
    }

    /**
     * Get field value from model, supporting dot notation and custom transformations
     */
    protected function getFieldValue(Model $model, $field): string
    {
        // Handle callable transformations
        if (is_callable($field)) {
            return $field($model);
        }

        // Handle array with transformation
        if (is_array($field)) {
            $value = data_get($model, $field['field']);
            if (isset($field['transform']) && is_callable($field['transform'])) {
                return $field['transform']($value, $model);
            }
            return $value ?? ($field['default'] ?? '-');
        }

        // Handle simple field name or dot notation
        $value = data_get($model, $field);
        return $value ?? '-';
    }

    /**
     * Handle the main export method logic
     */
    protected function handleExport(Request $request, string $modelClass, array $config): \Illuminate\Http\Response
    {
        $type = $request->get('type');
        
        if (!in_array($type, ['csv', 'pdf'])) {
            return abort(400, 'Invalid export type');
        }

        // Get the base query
        $query = $modelClass::query();
        
        // Apply any select fields for optimization
        if (isset($config['select_fields'])) {
            $query->select($config['select_fields']);
        }

        // Load relationships if specified
        if (isset($config['with_relations'])) {
            $query->with($config['with_relations']);
        }

        $models = $query->get();

        if ($type === 'csv') {
            return $this->generateCsvExport($models, $config['csv']);
        }

        if ($type === 'pdf') {
            return $this->generatePdfExport($models, $config['pdf']);
        }
    }

    /**
     * Handle print current view logic
     */
    protected function handlePrintCurrent(Request $request, string $modelClass, array $config): \Illuminate\Http\Response
    {
        $query = $modelClass::query();
        
        // Load relationships if specified
        if (isset($config['with_relations'])) {
            $query->with($config['with_relations']);
        }
        
        // Apply search and sort filters
        $query = $this->applySearchAndSort($query, $request, $config);
        
        $models = $query->get();
        
        return $this->generatePdfExport($models, $config['print_current']);
    }

    /**
     * Handle print all records logic
     */
    protected function handlePrintAll(Request $request, string $modelClass, array $config): \Illuminate\Http\Response
    {
        $query = $modelClass::query();
        
        // Load relationships if specified
        if (isset($config['with_relations'])) {
            $query->with($config['with_relations']);
        }
        
        // Apply default ordering for "all" records
        $defaultSort = $config['print_all']['default_sort'] ?? $config['default_sort'] ?? 'created_at';
        $query->orderBy($defaultSort);
        
        $models = $query->get();
        
        return $this->generatePdfExport($models, $config['print_all']);
    }
}
