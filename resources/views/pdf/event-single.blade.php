<x-printable-report
    title="Event Details"
    :data="[
        ['label' => 'Title', 'value' => $event->title],
        ['label' => 'Description', 'value' => $event->description],
        ['label' => 'Event Date', 'value' => $event->event_date],
        ['label' => 'Free Service', 'value' => $event->is_free_service ? 'Yes' : 'No'],
        ['label' => 'Broadcast Status', 'value' => $event->broadcast_status],
    ]"
    :columns="[
        ['key' => 'label', 'label' => 'Field', 'printWidth' => '30%'],
        ['key' => 'value', 'label' => 'Value', 'printWidth' => '70%'],
    ]"
    :header-info="[
        'logoSrc' => public_path('images/geraye_logo.jpeg'),
        'clinicName' => 'Geraye Home Care Services',
        'documentTitle' => 'Event Details',
    ]"
    :footer-info="[
        'generatedDate' => true,
    ]"
/>