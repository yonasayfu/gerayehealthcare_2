<?php

namespace App\DTOs;

use App\Services\Validation\BaseValidationRules;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UpdateStaffDTO extends BaseDTO
{
    public ?int $user_id;
    public ?string $employee_id;
    public ?string $first_name;
    public ?string $last_name;
    public ?string $email;
    public ?string $phone_number;
    public ?string $position;
    public ?string $department;
    public ?string $hire_date;
    public ?float $salary;
    public ?string $profile_photo_path;
    public ?string $address;
    public ?string $emergency_contact_name;
    public ?string $emergency_contact_phone;
    public ?string $employment_type;
    public ?string $status;
    public ?bool $is_active;
    public ?string $notes;

    private ?int $staffId = null;

    /**
     * Create DTO from request
     */
    public static function fromRequest(Request $request, ?int $staffId = null): self
    {
        $dto = self::create();
        $dto->staffId = $staffId;
        $dto->populate($request->validated());
        return $dto;
    }

    /**
     * Get validation rules
     */
    public function rules(): array
    {
        $staffId = $this->staffId;
        
        return [
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'employee_id' => [
                'sometimes', 
                'string', 
                'max:50', 
                Rule::unique('staff', 'employee_id')->ignore($staffId)
            ],
            'first_name' => ['sometimes', 'string', 'max:255'],
            'last_name' => ['sometimes', 'string', 'max:255'],
            'email' => [
                'sometimes', 
                'email', 
                'max:255', 
                Rule::unique('staff', 'email')->ignore($staffId)
            ],
            'phone_number' => ['nullable', 'string', 'max:20', BaseValidationRules::phoneNumber()],
            'position' => ['sometimes', 'string', 'max:255'],
            'department' => ['sometimes', 'string', 'max:255'],
            'hire_date' => ['sometimes', 'date', 'before_or_equal:today'],
            'salary' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'profile_photo_path' => ['nullable', 'string', 'max:2048'],
            'address' => ['nullable', 'string', 'max:500'],
            'emergency_contact_name' => ['nullable', 'string', 'max:255'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:20', BaseValidationRules::phoneNumber()],
            'employment_type' => ['sometimes', Rule::in(['full-time', 'part-time', 'contract', 'intern'])],
            'status' => ['sometimes', Rule::in(['active', 'inactive', 'terminated', 'on-leave'])],
            'is_active' => ['sometimes', 'boolean'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Get validation messages
     */
    public function messages(): array
    {
        return [
            'employee_id.unique' => 'This employee ID is already taken.',
            'first_name.string' => 'First name must be a valid string.',
            'last_name.string' => 'Last name must be a valid string.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email address is already registered.',
            'phone_number.regex' => 'Please enter a valid phone number.',
            'position.string' => 'Position must be a valid string.',
            'department.string' => 'Department must be a valid string.',
            'hire_date.date' => 'Please enter a valid hire date.',
            'hire_date.before_or_equal' => 'Hire date cannot be in the future.',
            'salary.numeric' => 'Salary must be a valid number.',
            'salary.min' => 'Salary cannot be negative.',
            'salary.max' => 'Salary amount is too large.',
            'employment_type.in' => 'Please select a valid employment type.',
            'status.in' => 'Please select a valid status.',
            'emergency_contact_phone.regex' => 'Please enter a valid emergency contact phone number.',
            'notes.max' => 'Notes cannot exceed 1000 characters.',
        ];
    }

    /**
     * Transform data before validation
     */
    public function transform(): array
    {
        $data = $this->toArray();
        
        // Remove null values for partial updates
        $data = array_filter($data, function ($value) {
            return $value !== null && $value !== '';
        });
        
        // Clean phone numbers if provided
        if (!empty($data['phone_number'])) {
            $data['phone_number'] = preg_replace('/[^0-9+\-\s\(\)]/', '', $data['phone_number']);
        }
        
        if (!empty($data['emergency_contact_phone'])) {
            $data['emergency_contact_phone'] = preg_replace('/[^0-9+\-\s\(\)]/', '', $data['emergency_contact_phone']);
        }
        
        return $data;
    }

    /**
     * Set staff ID for validation
     */
    public function setStaffId(int $staffId): self
    {
        $this->staffId = $staffId;
        return $this;
    }
}
