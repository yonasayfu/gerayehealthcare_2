<x-printable-report
    title="Marketing Tasks List"
    :data="$marketingTasks->map(function($task) {
        return [
            'task_code' => $task->task_code,
            'title' => $task->title,
            'campaign_name' => $task->campaign->campaign_name ?? '-',
            'assigned_to' => $task->assignedToStaff->user->name ?? '-',
            'task_type' => $task->task_type,
            'status' => $task->status,
            'scheduled_at' => $task->scheduled_at ? $task->scheduled_at->format('Y-m-d H:i') : '-',
        ];
    })->toArray()"
    :columns="[
        ['key' => 'task_code', 'label' => 'Task Code'],
        ['key' => 'title', 'label' => 'Title'],
        ['key' => 'campaign_name', 'label' => 'Campaign'],
        ['key' => 'assigned_to', 'label' => 'Assigned To'],
        ['key' => 'task_type', 'label' => 'Type'],
        ['key' => 'status', 'label' => 'Status'],
        ['key' => 'scheduled_at', 'label' => 'Scheduled At'],
    ]"
    :header-info="[
        'logoSrc' => public_path('images/geraye_logo.jpeg'),
        'clinicName' => 'Geraye Home Care Services',
        'documentTitle' => 'Marketing Tasks List',
    ]"
    :footer-info="[
        'generatedDate' => true,
    ]"
/>
