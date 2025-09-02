<?php

namespace App\Mcp;

use Illuminate\Support\Facades\Schema;
use PhpMcp\Laravel\AbstractMcpService;
use PhpMcp\Schema\Attributes\Parameter;
use PhpMcp\Schema\Attributes\Result;
use PhpMcp\Schema\Attributes\Tool;

class BoilerplateMcpService extends AbstractMcpService
{
    #[Tool(
        name: 'analyze_boilerplate_architecture',
        description: 'Analyze the Laravel boilerplate architecture and provide compliance score'
    )]
    #[Result(description: 'Architecture analysis results with compliance score and recommendations')]
    public function analyzeArchitecture(): array
    {
        $analysis = [
            'timestamp' => now()->toISOString(),
            'project' => 'Laravel Boilerplate',
            'components' => [],
            'compliance_score' => 0,
            'recommendations' => [],
        ];

        // Check for base components
        $analysis['components'] = [
            'base_controller' => class_exists(\App\Http\Controllers\BaseController::class),
            'optimized_base_controller' => class_exists(\App\Http\Controllers\OptimizedBaseController::class),
            'base_api_controller' => class_exists(\App\Http\Controllers\Api\V1\BaseApiController::class),
            'base_service' => class_exists(\App\Services\BaseService::class),
            'performance_optimized_service' => class_exists(\App\Services\PerformanceOptimizedBaseService::class),
            'base_dto' => class_exists(\App\DTOs\BaseDTO::class),
            'exception_hierarchy' => class_exists(\App\Exceptions\BaseException::class),
            'validation_rules' => class_exists(\App\Services\Validation\BaseValidationRules::class),
        ];

        // Calculate compliance score
        $implementedComponents = array_filter($analysis['components']);
        $analysis['compliance_score'] = round((count($implementedComponents) / count($analysis['components'])) * 100);

        // Add recommendations
        if (! $analysis['components']['performance_optimized_service']) {
            $analysis['recommendations'][] = 'Implement PerformanceOptimizedBaseService for caching';
        }

        if (! $analysis['components']['optimized_base_controller']) {
            $analysis['recommendations'][] = 'Use OptimizedBaseController for better performance';
        }

        return $analysis;
    }

    #[Tool(
        name: 'list_boilerplate_models',
        description: 'List all Eloquent models in the Laravel boilerplate'
    )]
    #[Result(description: 'List of model classes with their properties')]
    public function listModels(): array
    {
        $models = [];

        // Check if models directory exists
        $modelsPath = app_path('Models');
        if (is_dir($modelsPath)) {
            $files = scandir($modelsPath);
            foreach ($files as $file) {
                if (str_ends_with($file, '.php')) {
                    $modelName = str_replace('.php', '', $file);
                    $models[] = [
                        'name' => $modelName,
                        'path' => "app/Models/{$file}",
                        'namespace' => "App\\Models\\{$modelName}",
                    ];
                }
            }
        }

        return [
            'timestamp' => now()->toISOString(),
            'models' => $models,
            'count' => count($models),
        ];
    }

    #[Tool(
        name: 'check_database_tables',
        description: 'Check database tables and their columns'
    )]
    #[Parameter(name: 'table', description: 'Specific table to check (optional)', required: false)]
    #[Result(description: 'Database schema information')]
    public function checkDatabaseTables(?string $table = null): array
    {
        try {
            if ($table) {
                if (Schema::hasTable($table)) {
                    $columns = Schema::getColumnListing($table);

                    return [
                        'table' => $table,
                        'columns' => $columns,
                        'count' => count($columns),
                    ];
                } else {
                    return [
                        'error' => "Table {$table} not found",
                    ];
                }
            } else {
                $tables = Schema::getConnection()->getDoctrineSchemaManager()->listTableNames();

                return [
                    'tables' => $tables,
                    'count' => count($tables),
                ];
            }
        } catch (\Exception $e) {
            return [
                'error' => $e->getMessage(),
            ];
        }
    }

    #[Tool(
        name: 'get_boilerplate_statistics',
        description: 'Get statistics about the Laravel boilerplate'
    )]
    #[Result(description: 'Boilerplate statistics')]
    public function getStatistics(): array
    {
        return [
            'timestamp' => now()->toISOString(),
            'php_version' => phpversion(),
            'laravel_version' => app()->version(),
            'database_driver' => config('database.default'),
            'cache_driver' => config('cache.default'),
            'session_driver' => config('session.driver'),
            'queue_driver' => config('queue.default'),
        ];
    }
}
