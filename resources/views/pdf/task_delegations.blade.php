<x-printable-report
    title="Task Delegations Export – Geraye Home Care Services"
    :data="$tasks->map(function($t, $index) {
        return [
            'index' => $index + 1,
            'title' => $t->title,
            'assigned_to' => $t->assignee->first_name . ' ' . $t->assignee->last_name,
            'due_date' => \Carbon\Carbon::parse($t->due_date)->format('Y-m-d'),
            'status' => $t->status,
            'notes' => $t->notes,
        ];
    })->toArray()"
    :columns="[
        ['key' => 'index', 'label' => '#'],
        ['key' => 'title', 'label' => 'Title'],
        ['key' => 'assigned_to', 'label' => 'Assigned To'],
        ['key' => 'due_date', 'label' => 'Due Date'],
        ['key' => 'status', 'label' => 'Status'],
        ['key' => 'notes', 'label' => 'Notes'],
    ]"
    :header-info="[
        'logoSrc' => public_path('images/geraye_logo.jpeg'),
        'clinicName' => 'Geraye Home Care Services',
        'documentTitle' => 'Task Delegations Export',
    ]"
    :footer-info="[
        'generatedDate' => true,
    ]"
/>