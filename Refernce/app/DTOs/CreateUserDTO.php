<?php

namespace App\DTOs;

use App\Services\Validation\BaseValidationRules;
use Illuminate\Http\Request;

class CreateUserDTO extends BaseDTO
{
    /**
     * User data properties
     */
    public string $name;

    public string $email;

    public string $password;

    public ?string $phone_number;

    public bool $is_active;

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

        $this->name = $validated['name'];
        $this->email = $validated['email'];
        $this->password = $validated['password'];
        $this->phone_number = $validated['phone_number'] ?? null;
        $this->is_active = $validated['is_active'] ?? true;
    }

    /**
     * Reset DTO to default state
     */
    public function reset(): void
    {
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->phone_number = null;
        $this->is_active = true;
    }

    /**
     * Convert DTO to array
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
            'phone_number' => $this->phone_number,
            'is_active' => $this->is_active,
        ];
    }

    /**
     * Create DTO from request data
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public static function fromRequest($request): static
    {
        $data = $request->validate([
            'name' => BaseValidationRules::name(true),
            'email' => BaseValidationRules::email(true),
            'password' => BaseValidationRules::password(true),
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
        // Validate name
        if (empty($data['name']) || ! is_string($data['name'])) {
            throw new \InvalidArgumentException('Name is required and must be a string');
        }

        // Validate email
        if (empty($data['email']) || ! filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Valid email is required');
        }

        // Validate password
        if (empty($data['password']) || strlen($data['password']) < 8) {
            throw new \InvalidArgumentException('Password must be at least 8 characters long');
        }

        // Validate phone number if provided
        if (! empty($data['phone_number']) && ! preg_match('/^[\+]?[1-9][\d]{0,15}$/', $data['phone_number'])) {
            throw new \InvalidArgumentException('Phone number format is invalid');
        }

        // Validate is_active
        $is_active = filter_var($data['is_active'] ?? true, FILTER_VALIDATE_BOOLEAN);

        return [
            'name' => trim($data['name']),
            'email' => strtolower(trim($data['email'])),
            'password' => $data['password'],
            'phone_number' => ! empty($data['phone_number']) ? $data['phone_number'] : null,
            'is_active' => $is_active,
        ];
    }
}
