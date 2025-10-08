<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\LeaveRequest;

class StoreStaffLeaveRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'type' => 'required|string|in:Annual,Sick,Unpaid',
            'reason' => 'required|string|max:1000',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'start_date.required' => 'Please select a start date for your leave.',
            'start_date.date' => 'Please enter a valid start date.',
            'start_date.after_or_equal' => 'Start date must be today or in the future.',
            'end_date.required' => 'Please select an end date for your leave.',
            'end_date.date' => 'Please enter a valid end date.',
            'end_date.after_or_equal' => 'End date must be on or after the start date.',
            'type.required' => 'Please choose a leave type.',
            'type.in' => 'Leave type must be one of: Annual, Sick, Unpaid.',
            'reason.required' => 'Please provide a reason for your leave request.',
            'reason.max' => 'The reason cannot exceed 1000 characters.',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->start_date && $this->end_date) {
                $staffId = Auth::user()->staff->id ?? null;
                
                if ($staffId) {
                    // Check for overlapping leave requests
                    $overlappingRequest = LeaveRequest::where('staff_id', $staffId)
                        ->where('status', '!=', 'Denied')
                        ->where(function ($query) {
                            $query->whereBetween('start_date', [$this->start_date, $this->end_date])
                                ->orWhereBetween('end_date', [$this->start_date, $this->end_date])
                                ->orWhere(function ($q) {
                                    $q->where('start_date', '<=', $this->start_date)
                                        ->where('end_date', '>=', $this->end_date);
                                });
                        })
                        ->exists();

                    if ($overlappingRequest) {
                        $validator->errors()->add('start_date', 'You already have a leave request for these dates or overlapping dates.');
                    }
                }
            }
        });
    }
}