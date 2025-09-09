<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Response;

class OptimizedBaseController extends BaseController
{
    /**
     * Cache TTL in seconds
     */
    protected int $cacheTtl = 300; // 5 minutes

    /**
     * Enable or disable caching for this controller
     */
    protected bool $enableCaching = true;

    /**
     * Export formats supported
     */
    protected array $exportFormats = ['pdf', 'csv', 'xlsx'];

    /**
     * Create a new optimized controller instance
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get cached data or execute callback and cache result
     *
     * @param string $key
     * @param callable $callback
     * @param int|null $ttl
     * @return mixed
     */
    protected function remember(string $key, callable $callback, ?int $ttl = null)
    {
        if (!$this->enableCaching) {
            return $callback();
        }

        $ttl = $ttl ?? $this->cacheTtl;
        return Cache::remember($key, $ttl, $callback);
    }

    /**
     * Forget cached data by key
     *
     * @param string $key
     * @return bool
     */
    protected function forgetCache(string $key): bool
    {
        return Cache::forget($key);
    }

    /**
     * Clear all cache tags related to this controller
     *
     * @param array $tags
     * @return void
     */
    protected function clearCacheTags(array $tags): void
    {
        if (Cache::supportsTags()) {
            Cache::tags($tags)->flush();
        }
    }

    /**
     * Handle bulk operations
     *
     * @param Request $request
     * @param callable $operation
     * @param string $operationName
     * @return \Illuminate\Http\JsonResponse
     */
    protected function handleBulkOperation(Request $request, callable $operation, string $operationName)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'required|integer|exists:' . ($this->getModelTable()) . ',id',
        ]);

        $ids = $request->input('ids');
        $successCount = 0;
        $errors = [];

        foreach ($ids as $id) {
            try {
                $operation($id);
                $successCount++;
            } catch (\Exception $e) {
                $errors[] = [
                    'id' => $id,
                    'error' => $e->getMessage(),
                ];
            }
        }

        $message = sprintf(
            '%s operation completed. %d successful, %d failed.',
            ucfirst($operationName),
            $successCount,
            count($errors)
        );

        return $this->success([
            'processed' => $successCount,
            'failed' => count($errors),
            'errors' => $errors,
        ], $message);
    }

    /**
     * Export data in specified format
     *
     * @param Request $request
     * @param callable $dataProvider
     * @param string $filename
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\JsonResponse
     */
    protected function exportData(Request $request, callable $dataProvider, string $filename)
    {
        $format = $request->input('format', 'csv');

        if (!in_array($format, $this->exportFormats)) {
            return $this->error('Unsupported export format', 400);
        }

        $data = $dataProvider();

        switch ($format) {
            case 'pdf':
                return $this->exportToPdf($data, $filename);
            case 'csv':
                return $this->exportToCsv($data, $filename);
            case 'xlsx':
                return $this->exportToExcel($data, $filename);
            default:
                return $this->error('Export format not implemented', 501);
        }
    }

    /**
     * Export data to PDF
     *
     * @param mixed $data
     * @param string $filename
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    protected function exportToPdf($data, string $filename)
    {
        // This would typically use a PDF generation library like DomPDF
        // Implementation would depend on your specific requirements
        return $this->error('PDF export not implemented', 501);
    }

    /**
     * Export data to CSV
     *
     * @param mixed $data
     * @param string $filename
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    protected function exportToCsv($data, string $filename)
    {
        // This would typically use a CSV generation library
        // Implementation would depend on your specific requirements
        return $this->error('CSV export not implemented', 501);
    }

    /**
     * Export data to Excel
     *
     * @param mixed $data
     * @param string $filename
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    protected function exportToExcel($data, string $filename)
    {
        // This would typically use a Excel generation library like PhpSpreadsheet
        // Implementation would depend on your specific requirements
        return $this->error('Excel export not implemented', 501);
    }

    /**
     * Get the model table name for bulk operations validation
     *
     * @return string
     */
    protected function getModelTable(): string
    {
        // This should be overridden in child classes
        return 'models';
    }

    /**
     * Optimize database query with eager loading
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $relations
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function withOptimizedRelations($query, array $relations = [])
    {
        return $query->with($relations);
    }

    /**
     * Apply performance optimizations to query
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function optimizeQuery($query)
    {
        // Add any query optimizations here
        // For example, select only needed columns, add indexes, etc.
        return $query;
    }
}
