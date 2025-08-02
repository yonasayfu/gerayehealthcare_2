<x-printable-report
    title="Marketing Platform Details"
    :data="[
        ['label' => 'Platform Name', 'value' => $platform->name],
        ['label' => 'API Endpoint', 'value' => $platform->api_endpoint ?? '-'],
        ['label' => 'API Credentials', 'value' => $platform->api_credentials ?? '-'],
        ['label' => 'Is Active', 'value' => $platform->is_active ? 'Yes' : 'No'],
        ['label' => 'Created At', 'value' => $platform->created_at ? \Carbon\Carbon::parse($platform->created_at)->format('M d, Y H:i:s') : '-'],
        ['label' => 'Updated At', 'value' => $platform->updated_at ? \Carbon\Carbon::parse($platform->updated_at)->format('M d, Y H:i:s') : '-'],
    ]"
    :columns="[
        ['key' => 'label', 'label' => 'Field', 'printWidth' => '30%'],
        ['key' => 'value', 'label' => 'Value', 'printWidth' => '70%'],
    ]"
    :header-info="[
        'logoSrc' => public_path('images/geraye_logo.jpeg'),
        'clinicName' => 'Geraye Home Care Services',
        'documentTitle' => 'Marketing Platform Details',
    ]"
    :footer-info="[
        'generatedDate' => true,
    ]"
/>