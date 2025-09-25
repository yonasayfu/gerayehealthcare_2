<?php

namespace App\DTOs;

class CreateEventRecommendationDTO extends BaseDTO
{
    public function __construct(
        public int $event_id,
        public string $source_channel,
        public ?string $recommended_by_name,
        public ?string $recommended_by_phone,
        public string $patient_name,
        public ?string $phone_number,
        public ?string $notes,
        public string $status,
    ) {}

    /**
     * Hydrate DTO from validated array data.
     */
    public static function from(array $data): static
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
     * Convert DTO to array for persistence/service layers.
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
