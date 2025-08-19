<?php

namespace App\Services\Validation\Rules;

use Illuminate\Validation\Rule;

class EventStaffAssignmentRules extends BaseResourceRules
{
    public static function store(): array
    {
        return [
            'event_id' => 'required|exists:events,id',
            'staff_id' => [
                'required',
                'exists:staff,id',
                Rule::unique('event_staff_assignments')->where(function ($q) {
                    return $q->where('event_id', request('event_id'));
                }),
            ],
            'role' => 'required|string|max:255',
            'notes' => 'nullable|string',
        ];
    }

    public static function update($item): array
    {
        return [
            'event_id' => 'required|exists:events,id',
            'staff_id' => [
                'required',
                'exists:staff,id',
                Rule::unique('event_staff_assignments')
                    ->where(fn ($q) => $q->where('event_id', request('event_id')))
                    ->ignore($item->id),
            ],
            'role' => 'required|string|max:255',
            'notes' => 'nullable|string',
        ];
    }
}