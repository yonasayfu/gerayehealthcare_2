<x-printable-report
    title="Event Staff Assignment Details"
    :data="[
        ['label' => 'Event ID', 'value' => $assignment->event_id],
        ['label' => 'Staff ID', 'value' => $assignment->staff_id],
        ['label' => 'Role', 'value' => $assignment->role],
    ]"
    :columns="[
        ['key' => 'label', 'label' => 'Field', 'printWidth' => '30%'],
        ['key' => 'value', 'label' => 'Value', 'printWidth' => '70%'],
    ]"
    :header-info="[
        'logoSrc' => public_path('images/geraye_logo.jpeg'),
        'clinicName' => 'Geraye Home Care Services',
        'documentTitle' => 'Event Staff Assignment Details',
    ]"
    :footer-info="[
        'generatedDate' => true,
    ]"
/>