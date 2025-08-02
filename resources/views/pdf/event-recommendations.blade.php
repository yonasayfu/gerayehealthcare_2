<x-printable-report
    title="Event Recommendations List"
    :data="$recommendations->map(function($recommendation) {
        return [
            'event_id' => $recommendation->event_id,
            'source' => $recommendation->source,
            'recommended_by' => $recommendation->recommended_by,
            'patient_name' => $recommendation->patient_name,
            'patient_phone' => $recommendation->patient_phone,
            'status' => $recommendation->status,
        ];
    })->toArray()"
    :columns="[
        ['key' => 'event_id', 'label' => 'Event ID'],
        ['key' => 'source', 'label' => 'Source'],
        ['key' => 'recommended_by', 'label' => 'Recommended By'],
        ['key' => 'patient_name', 'label' => 'Patient Name'],
        ['key' => 'patient_phone', 'label' => 'Patient Phone'],
        ['key' => 'status', 'label' => 'Status'],
    ]"
    :header-info="[
        'logoSrc' => public_path('images/geraye_logo.jpeg'),
        'clinicName' => 'Geraye Home Care Services',
        'documentTitle' => 'Event Recommendations List',
    ]"
    :footer-info="[
        'generatedDate' => true,
    ]"
/>