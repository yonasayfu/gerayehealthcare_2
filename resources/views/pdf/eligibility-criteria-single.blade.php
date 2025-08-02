<x-printable-report
    title="Eligibility Criteria Details"
    :data="[
        ['label' => 'Event ID', 'value' => $eligibilityCriteria->event_id],
        ['label' => 'Criteria Name', 'value' => $eligibilityCriteria->criteria_name],
        ['label' => 'Operator', 'value' => $eligibilityCriteria->operator],
        ['label' => 'Value', 'value' => $eligibilityCriteria->value],
    ]"
    :columns="[
        ['key' => 'label', 'label' => 'Field', 'printWidth' => '30%'],
        ['key' => 'value', 'label' => 'Value', 'printWidth' => '70%'],
    ]"
    :header-info="[
        'logoSrc' => public_path('images/geraye_logo.jpeg'),
        'clinicName' => 'Geraye Home Care Services',
        'documentTitle' => 'Eligibility Criteria Details',
    ]"
    :footer-info="[
        'generatedDate' => true,
    ]"
/>