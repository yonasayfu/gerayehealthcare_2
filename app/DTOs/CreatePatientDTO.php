<?php

namespace App\DTOs;

class CreatePatientDTO
{
    public function __construct(
        public string $full_name,
        public ?string $fayda_id,
        public string $date_of_birth,
        public ?string $ethiopian_date_of_birth,
        public ?string $gender,
        public ?string $address,
        public ?string $phone_number,
        public ?string $email,
        public ?string $emergency_contact,
        public ?string $source,
        public ?string $geolocation,
        public ?int $registered_by_staff_id = null,
        public ?int $corporate_client_id = null,
        public ?int $policy_id = null
    ) {}

    /**
     * Create DTO from an array (works even if DTO has a constructor).
     *
     * @param array $data
     * @return self
     */
    public static function from(array $data): self
    {
        $ref = new \ReflectionClass(self::class);
        $dto = $ref->newInstanceWithoutConstructor();

        foreach ($data as $key => $value) {
            // set property if exists (accepts snake_case or camelCase keys)
            if ($ref->hasProperty($key)) {
                $prop = $ref->getProperty($key);
                $prop->setAccessible(true);
                $prop->setValue($dto, $value);
                continue;
            }

            // try camelCase conversion for snake_case input
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
     * Convert DTO to array (reads all defined properties).
     *
     * @return array
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
