<?php

namespace App\DTOs;

use App\Services\Validation\BaseValidationRules;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CreateStaffDTO extends BaseDTO
{
    public ?int $user_id;
    public string $employee_id;
    public string $first_name;
    public string $last_name;
    public string $email;
    public ?string $phone_number;
    public string $position;
    public string $department;
    public string $hire_date;
    public ?float $salary;
    public ?string $profile_photo_path;
    public ?string $address;
    public ?string $emergency_contact_name;
    public ?string $emergency_contact_phone;
    public string $employment_type;
    public string $status;
    public bool $is_active;
    public ?string $notes;

    /**
     * Create DTO from request
     */
    public static function fromRequest(Request $request): self
    {
        $dto = self::create();
        $dto->populate($request->validated());
        return $dto;
    }

    /**
     * Get validation rules
     */
    public function rules(): array
    {
        return [
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'employee_id' => ['required', 'string', 'max:50', 'unique:staff,employee_id'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:staff,email'],
            'phone_number' => ['nullable', 'string', 'max:20', BaseValidationRules::phoneNumber()],
            'position' => ['required', 'string', 'max:255'],
            'department' => ['required', 'string', 'max:255'],
            'hire_date' => ['required', 'date', 'before_or_equal:today'],
            'salary' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'profile_photo_path' => ['nullable', 'string', 'max:2048'],
            'address' => ['nullable', 'string', 'max:500'],
            'emergency_contact_name' => ['nullable', 'string', 'max:255'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:20', BaseValidationRules::phoneNumber()],
            'employment_type' => ['required', Rule::in(['full-time', 'part-time', 'contract', 'intern'])],
            'status' => ['required', Rule::in(['active', 'inactive', 'terminated', 'on-leave'])],
            'is_active' => ['boolean'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Get validation messages
     */
    public function messages(): array
    {
        return [
            'employee_id.required' => 'Employee ID is required.',
            'employee_id.unique' => 'This employee ID is already taken.',
            'first_name.required' => 'First name is required.',
            'last_name.required' => 'Last name is required.',
            'email.required' => 'Email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email address is already registered.',
            'phone_number.regex' => 'Please enter a valid phone number.',
            'position.required' => 'Position is required.',
            'department.required' => 'Department is required.',
            'hire_date.required' => 'Hire date is required.',
            'hire_date.date' => 'Please enter a valid hire date.',
            'hire_date.before_or_equal' => 'Hire date cannot be in the future.',
            'salary.numeric' => 'Salary must be a valid number.',
            'salary.min' => 'Salary cannot be negative.',
            'salary.max' => 'Salary amount is too large.',
            'employment_type.required' => 'Employment type is required.',
            'employment_type.in' => 'Please select a valid employment type.',
            'status.required' => 'Status is required.',
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
        
        // Generate employee ID if not provided
        if (empty($data['employee_id'])) {
            $data['employee_id'] = \App\Models\Staff::generateEmployeeId();
        }
        
        // Set default values
        $data['is_active'] = $data['is_active'] ?? true;
        $data['status'] = $data['status'] ?? 'active';
        $data['employment_type'] = $data['employment_type'] ?? 'full-time';
        
        // Clean phone numbers
        if (!empty($data['phone_number'])) {
            $data['phone_number'] = preg_replace('/[^0-9+\-\s\(\)]/', '', $data['phone_number']);
        }
        
        if (!empty($data['emergency_contact_phone'])) {
            $data['emergency_contact_phone'] = preg_replace('/[^0-9+\-\s\(\)]/', '', $data['emergency_contact_phone']);
        }
        
        return $data;
    }
}
