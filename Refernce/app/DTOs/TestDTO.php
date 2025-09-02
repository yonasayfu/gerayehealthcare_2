<?php

namespace App\DTOs;

class TestDTO extends BaseDTO
{
    /**
     * Test data properties
     */
    public int $id;

    public string $name;

    public ?string $description;

    public array $tags;

    /**
     * Create a new DTO instance
     */
    public function __construct(array $data = [])
    {
        $this->populate($data);
    }

    /**
     * Populate DTO with data
     */
    public function populate(array $data): void
    {
        $this->id = $data['id'] ?? 0;
        $this->name = $data['name'] ?? '';
        $this->description = $data['description'] ?? null;
        $this->tags = $data['tags'] ?? [];
    }

    /**
     * Reset DTO to default state
     */
    public function reset(): void
    {
        $this->id = 0;
        $this->name = '';
        $this->description = null;
        $this->tags = [];
    }

    /**
     * Convert DTO to array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'tags' => $this->tags,
        ];
    }

    /**
     * Validate DTO data
     */
    public static function validate(array $data): array
    {
        $validated = [];

        // Validate ID
        $validated['id'] = filter_var($data['id'] ?? 0, FILTER_VALIDATE_INT) ?: 0;

        // Validate name
        $validated['name'] = trim($data['name'] ?? '');
        if (empty($validated['name'])) {
            throw new \InvalidArgumentException('Name is required');
        }

        // Validate description
        $validated['description'] = ! empty($data['description']) ? trim($data['description']) : null;

        // Validate tags
        $validated['tags'] = is_array($data['tags'] ?? null) ? $data['tags'] : [];

        return $validated;
    }
}
