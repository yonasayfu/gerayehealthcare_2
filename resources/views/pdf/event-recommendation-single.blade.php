<x-printable-report
    title="Event Recommendation Details"
    :data="[
        ['label' => 'Event ID', 'value' => $eventRecommendation->event_id],
        ['label' => 'Source', 'value' => $eventRecommendation->source],
        ['label' => 'Recommended By', 'value' => $eventRecommendation->recommended_by],
        ['label' => 'Patient Name', 'value' => $eventRecommendation->patient_name],
        ['label' => 'Patient Phone', 'value' => $eventRecommendation->patient_phone],
        ['label' => 'Notes', 'value' => $eventRecommendation->notes],
        ['label' => 'Status', 'value' => $eventRecommendation->status],
    ]"
    :columns="[
        ['key' => 'label', 'label' => 'Field', 'printWidth' => '30%'],
        ['key' => 'value', 'label' => 'Value', 'printWidth' => '70%'],
    ]"
    :header-info="[
        'logoSrc' => public_path('images/geraye_logo.jpeg'),
        'clinicName' => 'Geraye Home Care Services',
        'documentTitle' => 'Event Recommendation Details',
    ]"
    :footer-info="[
        'generatedDate' => true,
    ]"
/>