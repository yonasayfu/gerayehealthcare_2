<x-printable-report
    title="Eligibility Criteria List"
    :data="$criteria->map(function($criterion) {
        return [
            'event_id' => $criterion->event_id,
            'criteria_name' => $criterion->criteria_name,
            'operator' => $criterion->operator,
            'value' => $criterion->value,
        ];
    })->toArray()"
    :columns="[
        ['key' => 'event_id', 'label' => 'Event ID'],
        ['key' => 'criteria_name', 'label' => 'Criteria Name'],
        ['key' => 'operator', 'label' => 'Operator'],
        ['key' => 'value', 'label' => 'Value'],
    ]"
    :header-info="[
        'logoSrc' => public_path('images/geraye_logo.jpeg'),
        'clinicName' => 'Geraye Home Care Services',
        'documentTitle' => 'Eligibility Criteria List',
    ]"
    :footer-info="[
        'generatedDate' => true,
    ]"
/>