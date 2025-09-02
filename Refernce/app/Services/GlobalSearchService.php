<?php

namespace App\Services;

use App\Models\Message;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Support\Collection;

class GlobalSearchService extends PerformanceOptimizedBaseService
{
    protected array $searchableEntities = [
        'users' => [
            'model' => User::class,
            'fields' => ['name', 'email'],
            'icon' => 'user',
            'category' => 'Users',
            'route_name' => 'admin.users.show',
        ],
        'staff' => [
            'model' => Staff::class,
            'fields' => ['first_name', 'last_name', 'email', 'position'],
            'icon' => 'user-check',
            'category' => 'Staff',
            'route_name' => 'admin.staff.show',
        ],
        'messages' => [
            'model' => Message::class,
            'fields' => ['message'],
            'icon' => 'message-circle',
            'category' => 'Messages',
            'route_name' => 'messages.show',
        ],
    ];

    public function __construct()
    {
        // No specific model for this service
    }

    /**
     * Perform global search across multiple entities
     */
    public function search(string $query, ?array $entities = null, int $limit = 20): array
    {
        if (empty($query) || strlen($query) < 2) {
            return [];
        }

        $entities = $entities ?? array_keys($this->searchableEntities);
        $cacheKey = 'global_search_'.md5($query.implode(',', $entities).$limit);

        return $this->getCachedData($cacheKey, function () use ($query, $entities, $limit) {
            $results = collect();

            foreach ($entities as $entity) {
                if (! isset($this->searchableEntities[$entity])) {
                    continue;
                }

                $entityResults = $this->searchEntity($entity, $query, $limit);
                $results = $results->merge($entityResults);
            }

            return $results->sortByDesc('relevance')
                ->take($limit)
                ->values()
                ->toArray();
        }, 300); // Cache for 5 minutes
    }

    /**
     * Search within a specific entity
     */
    protected function searchEntity(string $entity, string $query, int $limit): Collection
    {
        $config = $this->searchableEntities[$entity];
        $model = $config['model'];
        $fields = $config['fields'];

        $queryBuilder = $model::query();

        // Build search conditions
        $queryBuilder->where(function ($q) use ($fields, $query) {
            foreach ($fields as $field) {
                $q->orWhere($field, 'LIKE', "%{$query}%");
            }
        });

        // Add specific constraints for each entity
        switch ($entity) {
            case 'users':
                $queryBuilder->where('is_active', true)
                    ->select('id', 'name', 'email', 'profile_photo_path', 'created_at');
                break;

            case 'staff':
                $queryBuilder->select('id', 'first_name', 'last_name', 'email', 'position', 'department', 'created_at');
                break;

            case 'messages':
                $queryBuilder->with(['sender:id,name', 'receiver:id,name'])
                    ->select('id', 'sender_id', 'receiver_id', 'message', 'created_at')
                    ->whereNotNull('message');
                break;
        }

        $results = $queryBuilder->limit($limit)->get();

        return $results->map(function ($item) use ($entity, $config, $query) {
            return $this->formatSearchResult($item, $entity, $config, $query);
        });
    }

    /**
     * Format search result for consistent output
     */
    protected function formatSearchResult($item, string $entity, array $config, string $query): array
    {
        $result = [
            'id' => $item->id,
            'type' => ucfirst($entity),
            'category' => $config['category'],
            'icon' => $config['icon'],
            'relevance' => $this->calculateRelevance($item, $config['fields'], $query),
            'created_at' => $item->created_at,
        ];

        // Entity-specific formatting
        switch ($entity) {
            case 'users':
                $result['title'] = $item->name;
                $result['description'] = $item->email;
                $result['avatar'] = $item->profile_photo_path;
                $result['url'] = route($config['route_name'], $item->id);
                break;

            case 'staff':
                $fullName = trim($item->first_name.' '.$item->last_name);
                $result['title'] = $fullName ?: 'Staff Member';
                $result['description'] = ($item->position ?? 'Staff').
                    ($item->department ? " • {$item->department}" : '').
                    ($item->email ? " • {$item->email}" : '');
                $result['url'] = route($config['route_name'], $item->id);
                break;

            case 'messages':
                $messagePreview = strlen($item->message) > 100
                    ? substr($item->message, 0, 100).'...'
                    : $item->message;
                $result['title'] = "Message from {$item->sender->name}";
                $result['description'] = $messagePreview;
                $result['url'] = route($config['route_name'], $item->id);
                $result['metadata'] = [
                    'sender' => $item->sender->name,
                    'receiver' => $item->receiver->name,
                ];
                break;
        }

        return $result;
    }

    /**
     * Calculate relevance score for search result
     */
    protected function calculateRelevance($item, array $fields, string $query): int
    {
        $score = 0;
        $queryLower = strtolower($query);

        foreach ($fields as $field) {
            $fieldValue = strtolower($item->$field ?? '');

            // Exact match gets highest score
            if ($fieldValue === $queryLower) {
                $score += 100;
            }
            // Starts with query gets high score
            elseif (str_starts_with($fieldValue, $queryLower)) {
                $score += 80;
            }
            // Contains query gets medium score
            elseif (str_contains($fieldValue, $queryLower)) {
                $score += 60;
            }
        }

        return $score;
    }

    /**
     * Get search suggestions
     */
    public function getSuggestions(string $query, ?string $entity = null, int $limit = 5): array
    {
        if (empty($query) || strlen($query) < 1) {
            return [];
        }

        $entities = $entity ? [$entity] : array_keys($this->searchableEntities);
        $cacheKey = 'search_suggestions_'.md5($query.implode(',', $entities).$limit);

        return $this->getCachedData($cacheKey, function () use ($query, $entities, $limit) {
            $suggestions = collect();

            foreach ($entities as $entityName) {
                if (! isset($this->searchableEntities[$entityName])) {
                    continue;
                }

                $config = $this->searchableEntities[$entityName];
                $model = $config['model'];
                $fields = $config['fields'];

                $results = $model::query()
                    ->where(function ($q) use ($fields, $query) {
                        foreach ($fields as $field) {
                            $q->orWhere($field, 'LIKE', "{$query}%");
                        }
                    })
                    ->limit($limit)
                    ->get();

                foreach ($results as $result) {
                    foreach ($fields as $field) {
                        $value = $result->$field;
                        if ($value && str_starts_with(strtolower($value), strtolower($query))) {
                            $suggestions->push([
                                'text' => $value,
                                'entity' => $entityName,
                                'type' => ucfirst($entityName),
                            ]);
                        }
                    }
                }
            }

            return $suggestions->unique('text')
                ->take($limit)
                ->values()
                ->toArray();
        }, 300); // Cache for 5 minutes
    }

    /**
     * Get search statistics
     */
    public function getSearchStats(): array
    {
        $cacheKey = 'search_stats';

        return $this->getCachedData($cacheKey, function () {
            $stats = [];

            foreach ($this->searchableEntities as $entity => $config) {
                $model = $config['model'];
                $count = $model::count();

                $stats[$entity] = [
                    'name' => ucfirst($entity),
                    'count' => $count,
                    'icon' => $config['icon'],
                    'category' => $config['category'],
                ];
            }

            return $stats;
        }, 3600); // Cache for 1 hour
    }

    /**
     * Get available search entities
     */
    public function getAvailableEntities(): array
    {
        return collect($this->searchableEntities)
            ->map(function ($config, $key) {
                return [
                    'key' => $key,
                    'name' => ucfirst($key),
                    'category' => $config['category'],
                    'icon' => $config['icon'],
                    'fields' => $config['fields'],
                ];
            })
            ->values()
            ->toArray();
    }

    /**
     * Add custom searchable entity
     */
    public function addSearchableEntity(string $key, array $config): void
    {
        $this->searchableEntities[$key] = $config;
    }

    /**
     * Remove searchable entity
     */
    public function removeSearchableEntity(string $key): void
    {
        unset($this->searchableEntities[$key]);
    }
}
