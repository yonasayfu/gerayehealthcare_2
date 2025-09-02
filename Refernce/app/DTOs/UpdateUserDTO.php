<?php

namespace App\DTOs;

use App\Services\Validation\BaseValidationRules;
use Illuminate\Http\Request;

class UpdateUserDTO extends BaseDTO
{
    /**
     * User data properties
     */
    public ?string $name;

    public ?string $email;

    public ?string $password;

    public ?string $phone_number;

    public ?bool $is_active;

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
        $validated = self::validate($data);

        $this->name = $validated['name'] ?? null;
        $this->email = $validated['email'] ?? null;
        $this->password = $validated['password'] ?? null;
        $this->phone_number = $validated['phone_number'] ?? null;
        $this->is_active = $validated['is_active'] ?? null;
    }

    /**
     * Reset DTO to default state
     */
    public function reset(): void
    {
        $this->name = null;
        $this->email = null;
        $this->password = null;
        $this->phone_number = null;
        $this->is_active = null;
    }

    /**
     * Convert DTO to array
     */
    public function toArray(): array
    {
        $data = [];

        if ($this->name !== null) {
            $data['name'] = $this->name;
        }

        if ($this->email !== null) {
            $data['email'] = $this->email;
        }

        if ($this->password !== null) {
            $data['password'] = $this->password;
        }

        if ($this->phone_number !== null) {
            $data['phone_number'] = $this->phone_number;
        }

        if ($this->is_active !== null) {
            $data['is_active'] = $this->is_active;
        }

        return $data;
    }

    /**
     * Create DTO from request data
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public static function fromRequest($request): static
    {
        $data = $request->validate([
            'name' => BaseValidationRules::name(false),
            'email' => BaseValidationRules::email(false),
            'password' => BaseValidationRules::password(false),
            'phone_number' => BaseValidationRules::phone(false),
            'is_active' => BaseValidationRules::boolean(false),
        ]);

        return static::create($data);
    }

    /**
     * Validate DTO data
     */
    public static function validate(array $data): array
    {
        $validated = [];

        // Validate name if provided
        if (array_key_exists('name', $data)) {
            if ($data['name'] !== null && (! is_string($data['name']) || empty(trim($data['name'])))) {
                throw new \InvalidArgumentException('Name must be a non-empty string or null');
            }
            $validated['name'] = $data['name'] !== null ? trim($data['name']) : null;
        }

        // Validate email if provided
        if (array_key_exists('email', $data)) {
            if ($data['email'] !== null && ! filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                throw new \InvalidArgumentException('Email must be valid or null');
            }
            $validated['email'] = $data['email'] !== null ? strtolower(trim($data['email'])) : null;
        }

        // Validate password if provided
        if (array_key_exists('password', $data)) {
            if ($data['password'] !== null && strlen($data['password']) < 8) {
                throw new \InvalidArgumentException('Password must be at least 8 characters long or null');
            }
            $validated['password'] = $data['password'] !== null ? $data['password'] : null;
        }

        // Validate phone number if provided
        if (array_key_exists('phone_number', $data)) {
            if ($data['phone_number'] !== null && $data['phone_number'] !== '' && $data['phone_number'] !== '?' && ! preg_match('/^[\+]?[1-9][\d]{0,15}$/', $data['phone_number'])) {
                throw new \InvalidArgumentException('Phone number format is invalid');
            }
            $validated['phone_number'] = ($data['phone_number'] !== null && $data['phone_number'] !== '' && $data['phone_number'] !== '?') ? $data['phone_number'] : null;
        }

        // Validate is_active if provided
        if (array_key_exists('is_active', $data)) {
            $validated['is_active'] = $data['is_active'] !== null ? filter_var($data['is_active'], FILTER_VALIDATE_BOOLEAN) : null;
        }

        return $validated;
    }
}
