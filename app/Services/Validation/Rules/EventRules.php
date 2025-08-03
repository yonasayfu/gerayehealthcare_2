<?php

namespace App\Services\Validation\Rules;

class EventRules extends BaseResourceRules
{
    public static function store(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'location' => 'nullable|string|max:255',
            'capacity' => 'nullable|integer|min:1',
            'is_public' => 'boolean',
            'status' => 'required|string|in:Draft,Scheduled,In Progress,Completed,Cancelled',
        ];
    }
    
    public static function update($event): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'location' => 'nullable|string|max:255',
            'capacity' => 'nullable|integer|min:1',
            'is_public' => 'boolean',
            'status' => 'required|string|in:Draft,Scheduled,In Progress,Completed,Cancelled',
        ];
    }
}
