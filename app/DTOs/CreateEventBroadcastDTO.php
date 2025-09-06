<?php

namespace App\DTOs;

class CreateEventBroadcastDTO extends BaseDTO
{
    public function __construct(
        public int $event_id,
        public string $channel,
        public string $message,
        public int $sent_by_staff_id
    ) {}

    /**
     * Hydrate DTO from array, tolerant to snake_case or camelCase keys.
     */
    public static function from(array $data): self
    {
        $ref = new \ReflectionClass(self::class);
        $dto = $ref->newInstanceWithoutConstructor();

        foreach ($data as $key => $value) {
            if ($ref->hasProperty($key)) {
                $prop = $ref->getProperty($key);
                $prop->setAccessible(true);
                $prop->setValue($dto, $value);

                continue;
            }

            $camel = lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $key))));
            if ($ref->hasProperty($camel)) {
                $prop = $ref->getProperty($camel);
                $prop->setAccessible(true);
                $prop->setValue($dto, $value);
            }
        }

        return $dto;
    }

    /**
     * Convert DTO to array of public properties.
     */
    public function toArray(): array
    {
        $ref = new \ReflectionObject($this);
        $props = $ref->getProperties();

        $data = [];
        foreach ($props as $prop) {
            $prop->setAccessible(true);
            $name = $prop->getName();
            $data[$name] = $prop->getValue($this);
        }

        return $data;
    }
}
