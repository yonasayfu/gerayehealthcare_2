<x-printable-report
    title="Event Participants List"
    :data="$participants->map(function($participant) {
        return [
            'event_id' => $participant->event_id,
            'patient_id' => $participant->patient_id,
            'status' => $participant->status,
        ];
    })->toArray()"
    :columns="[
        ['key' => 'event_id', 'label' => 'Event ID'],
        ['key' => 'patient_id', 'label' => 'Patient ID'],
        ['key' => 'status', 'label' => 'Status'],
    ]"
    :header-info="[
        'logoSrc' => public_path('images/geraye_logo.jpeg'),
        'clinicName' => 'Geraye Home Care Services',
        'documentTitle' => 'Event Participants List',
    ]"
    :footer-info="[
        'generatedDate' => true,
    ]"
/>