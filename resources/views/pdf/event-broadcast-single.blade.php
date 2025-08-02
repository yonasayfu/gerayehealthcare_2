<x-printable-report
    title="Event Broadcast Details"
    :data="[
        ['label' => 'Event ID', 'value' => $broadcast->event_id],
        ['label' => 'Channel', 'value' => $broadcast->channel],
        ['label' => 'Message', 'value' => $broadcast->message],
        ['label' => 'Sent By Staff ID', 'value' => $broadcast->sent_by_staff_id],
    ]"
    :columns="[
        ['key' => 'label', 'label' => 'Field', 'printWidth' => '30%'],
        ['key' => 'value', 'label' => 'Value', 'printWidth' => '70%'],
    ]"
    :header-info="[
        'logoSrc' => public_path('images/geraye_logo.jpeg'),
        'clinicName' => 'Geraye Home Care Services',
        'documentTitle' => 'Event Broadcast Details',
    ]"
    :footer-info="[
        'generatedDate' => true,
    ]"
/>