<?php

namespace App\Http\Requests;

use App\Services\Validation\Rules\TaskDelegationRules;
use Illuminate\Foundation\Http\FormRequest;

class StoreStaffTaskDelegationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null && $this->user()->staff !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return TaskDelegationRules::store();
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'The task title is required.',
            'title.string' => 'The task title must be a string.',
            'title.max' => 'The task title cannot exceed 255 characters.',
            'assigned_to.required' => 'The assigned staff member is required.',
            'assigned_to.exists' => 'The selected staff member does not exist.',
            'due_date.required' => 'The due date is required.',
            'due_date.date' => 'The due date must be a valid date.',
            'status.required' => 'The task status is required.',
            'status.in' => 'The status must be one of: Pending, In Progress, Completed.',
            'notes.string' => 'The notes must be a string.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'assigned_to' => 'assigned staff member',
            'due_date' => 'due date',
        ];
    }
}
