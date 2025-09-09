<?php

namespace App\DTOs;

use App\Services\Validation\BaseValidationRules;
use Illuminate\Http\Request;

class CreateRoleDTO extends BaseDTO
{
    public string $name;
    public string $guard_name;
    public array $permissions;

    /**
     * Create DTO from HTTP request
     */
    public static function fromRequest(Request $request): self
    {
        $dto = self::create();
        $dto->populate([
            'name' => $request->input('name'),
            'guard_name' => $request->input('guard_name', 'web'),
            'permissions' => $request->input('permissions', []),
        ]);
        
        $dto->validate();
        return $dto;
    }

    /**
     * Validation rules
     */
    protected function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:roles,name',
                'regex:/^[a-z0-9\-_]+$/', // Only lowercase, numbers, hyphens, underscores
            ],
            'guard_name' => 'required|string|max:255',
            'permissions' => 'array',
            'permissions.*' => 'string|exists:permissions,name',
        ];
    }

    /**
     * Custom validation messages
     */
    protected function messages(): array
    {
        return [
            'name.required' => 'Role name is required',
            'name.unique' => 'A role with this name already exists',
            'name.regex' => 'Role name can only contain lowercase letters, numbers, hyphens, and underscores',
            'permissions.*.exists' => 'One or more selected permissions do not exist',
        ];
    }

    /**
     * Transform data before validation
     */
    protected function transform(): void
    {
        // Convert name to lowercase and replace spaces with hyphens
        if (isset($this->name)) {
            $this->name = strtolower(str_replace(' ', '-', trim($this->name)));
        }

        // Ensure permissions is an array
        if (!is_array($this->permissions)) {
            $this->permissions = [];
        }
    }
}
