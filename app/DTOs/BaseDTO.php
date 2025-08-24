<?php

namespace App\DTOs;

abstract class BaseDTO
{
    protected static $instances = [];
    
    public static function from(array $data): static
    {
        // Simple object pooling to reduce instantiation overhead
        $hash = md5(serialize($data) . static::class);
        
        if (!isset(static::$instances[$hash])) {
            static::$instances[$hash] = new static(...array_values($data));
        }
        
        return static::$instances[$hash];
    }
    
    public function toArray(): array
    {
        $reflection = new \ReflectionClass($this);
        $properties = $reflection->getProperties(\ReflectionProperty::IS_PUBLIC);
        
        $data = [];
        foreach ($properties as $property) {
            $data[$property->getName()] = $property->getValue($this);
        }
        
        return $data;
    }
    
    // Clear instances periodically to prevent memory leaks
    public static function clearCache(): void
    {
        static::$instances = [];
    }
}