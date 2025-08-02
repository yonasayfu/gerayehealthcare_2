<x-printable-report
    title="Event Participant Details"
    :data="[
        ['label' => 'Event ID', 'value' => $participant->event_id],
        ['label' => 'Patient ID', 'value' => $participant->patient_id],
        ['label' => 'Status', 'value' => $participant->status],
    ]"
    :columns="[
        ['key' => 'label', 'label' => 'Field', 'printWidth' => '30%'],
        ['key' => 'value', 'label' => 'Value', 'printWidth' => '70%'],
    ]"
    :header-info="[
        'logoSrc' => public_path('images/geraye_logo.jpeg'),
        'clinicName' => 'Geraye Home Care Services',
        'documentTitle' => 'Event Participant Details',
    ]"
    :footer-info="[
        'generatedDate' => true,
    ]"
/>