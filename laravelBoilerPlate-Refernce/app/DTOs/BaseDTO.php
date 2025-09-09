<?php

namespace App\DTOs;

abstract class BaseDTO
{
    /**
     * Pool of reusable DTO instances for memory optimization
     */
    protected static array $instancePool = [];

    /**
     * Create a new DTO instance or reuse from pool
     *
     * @param array $data
     * @return static
     */
    public static function create(array $data = []): static
    {
        // Check if we have a reusable instance in the pool
        $className = static::class;
        if (!empty(self::$instancePool[$className])) {
            $instance = array_pop(self::$instancePool[$className]);
            $instance->populate($data);
            return $instance;
        }

        // Create new instance
        return new static($data);
    }

    /**
     * Return instance to pool for reuse
     *
     * @return void
     */
    public function release(): void
    {
        $className = static::class;
        if (!isset(self::$instancePool[$className])) {
            self::$instancePool[$className] = [];
        }

        // Reset properties to default values
        $this->reset();

        // Add to pool if not already at maximum size
        if (count(self::$instancePool[$className]) < 10) {
            self::$instancePool[$className][] = $this;
        }
    }

    /**
     * Populate DTO with data
     *
     * @param array $data
     * @return void
     */
    abstract public function populate(array $data): void;

    /**
     * Reset DTO to default state
     *
     * @return void
     */
    abstract public function reset(): void;

    /**
     * Convert DTO to array
     *
     * @return array
     */
    abstract public function toArray(): array;

    /**
     * Create DTO from request data
     *
     * @param \Illuminate\Http\Request $request
     * @return static
     */
    public static function fromRequest($request): static
    {
        return static::create($request->all());
    }

    /**
     * Validate DTO data
     *
     * @param array $data
     * @return array
     */
    public static function validate(array $data): array
    {
        // Default implementation - should be overridden in child classes
        return $data;
    }
}
