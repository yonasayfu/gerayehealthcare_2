<x-printable-report
    title="Event Staff Assignments List"
    :data="$assignments->map(function($assignment) {
        return [
            'event_id' => $assignment->event_id,
            'staff_id' => $assignment->staff_id,
            'role' => $assignment->role,
        ];
    })->toArray()"
    :columns="[
        ['key' => 'event_id', 'label' => 'Event ID'],
        ['key' => 'staff_id', 'label' => 'Staff ID'],
        ['key' => 'role', 'label' => 'Role'],
    ]"
    :header-info="[
        'logoSrc' => public_path('images/geraye_logo.jpeg'),
        'clinicName' => 'Geraye Home Care Services',
        'documentTitle' => 'Event Staff Assignments List',
    ]"
    :footer-info="[
        'generatedDate' => true,
    ]"
/>