<?php

namespace App\DTOs;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

abstract class BaseDTO
{
    /**
     * Pool of reusable DTO instances for memory optimization
     */
    protected static array $instancePool = [];

    /**
     * Maximum pool size per DTO class
     */
    protected static int $maxPoolSize = 10;

    /**
     * Legacy instances cache for backward compatibility
     */
    protected static $instances = [];

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
     * Legacy method for backward compatibility
     */
    public static function from(array $data): static
    {
        return static::create($data);
    }

    /**
     * Create DTO with validation
     *
     * @param array $data
     * @return static
     * @throws ValidationException
     */
    public static function createValidated(array $data): static
    {
        $validatedData = static::validate($data);
        return static::create($validatedData);
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
        if (count(self::$instancePool[$className]) < static::$maxPoolSize) {
            self::$instancePool[$className][] = $this;
        }
    }

    /**
     * Constructor
     *
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        if (!empty($data)) {
            $this->populate($data);
        }
    }

    /**
     * Populate DTO with data
     *
     * @param array $data
     * @return void
     */
    public function populate(array $data): void
    {
        foreach ($data as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (method_exists($this, $method)) {
                $this->$method($value);
            } elseif (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

    /**
     * Reset DTO to default state
     *
     * @return void
     */
    public function reset(): void
    {
        $reflection = new \ReflectionClass($this);
        $properties = $reflection->getProperties();

        foreach ($properties as $property) {
            if (!$property->isStatic()) {
                $property->setAccessible(true);
                $property->setValue($this, null);
            }
        }
    }

    /**
     * Convert DTO to array
     *
     * @return array
     */
    public function toArray(): array
    {
        $reflection = new \ReflectionClass($this);
        $properties = $reflection->getProperties();

        $data = [];
        foreach ($properties as $property) {
            if (!$property->isStatic()) {
                $property->setAccessible(true);
                $value = $property->getValue($this);
                if ($value !== null) {
                    $data[$property->getName()] = $value;
                }
            }
        }

        return $data;
    }

    /**
     * Get validation rules for this DTO
     *
     * @return array
     */
    public static function rules(): array
    {
        return [];
    }

    /**
     * Validate DTO data
     *
     * @param array $data
     * @return array
     * @throws ValidationException
     */
    public static function validate(array $data): array
    {
        $rules = static::rules();
        if (empty($rules)) {
            return $data;
        }

        $validator = Validator::make($data, $rules, static::messages());

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }

    /**
     * Get custom validation messages
     *
     * @return array
     */
    public static function messages(): array
    {
        return [];
    }

    /**
     * Create DTO from request data
     *
     * @param Request $request
     * @return static
     */
    public static function fromRequest(Request $request): static
    {
        return static::create($request->all());
    }

    /**
     * Clear all instance pools and legacy cache
     *
     * @return void
     */
    public static function clearCache(): void
    {
        self::$instancePool = [];
        static::$instances = [];
    }

    /**
     * Get pool statistics
     *
     * @return array
     */
    public static function getPoolStats(): array
    {
        $stats = [];
        foreach (self::$instancePool as $className => $pool) {
            $stats[$className] = count($pool);
        }
        return $stats;
    }

    /**
     * Convert to JSON
     *
     * @return string
     */
    public function toJson(): string
    {
        return json_encode($this->toArray());
    }

    /**
     * Check if DTO has valid data
     *
     * @return bool
     */
    public function isValid(): bool
    {
        try {
            static::validate($this->toArray());
            return true;
        } catch (ValidationException $e) {
            return false;
        }
    }
}