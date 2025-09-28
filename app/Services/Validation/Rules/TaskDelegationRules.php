<?php

namespace App\Services\Validation\Rules;

class TaskDelegationRules extends BaseResourceRules
{
    public static function store(): array
    {
        return [
            'title' => 'required|string|max:255',
            'assigned_to' => 'required|exists:staff,id',
            'due_date' => 'required|date',
            'status' => 'required|in:Pending,In Progress,Completed',
            'notes' => 'nullable|string',
            'priority_level' => 'required|integer|min:1|max:5',
            'task_category' => 'sometimes|nullable|string|max:100',
            'partner_id' => 'sometimes|nullable|exists:partners,id',
        ];
    }

    public static function update($item): array
    {
        return self::store();
    }
}
