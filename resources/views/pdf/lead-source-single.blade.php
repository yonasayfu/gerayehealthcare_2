<x-printable-report
    title="Lead Source Details"
    :data="[
        ['label' => 'Name', 'value' => $leadSource->name],
        ['label' => 'Category', 'value' => $leadSource->category],
        ['label' => 'Description', 'value' => $leadSource->description],
        ['label' => 'Active', 'value' => $leadSource->is_active ? 'Yes' : 'No'],
        ['label' => 'Created At', 'value' => \Carbon\Carbon::parse($leadSource->created_at)->format('M d, Y H:i')],
        ['label' => 'Updated At', 'value' => \Carbon\Carbon::parse($leadSource->updated_at)->format('M d, Y H:i')],
    ]"
    :columns="[
        ['key' => 'label', 'label' => 'Field', 'printWidth' => '30%'],
        ['key' => 'value', 'label' => 'Value', 'printWidth' => '70%'],
    ]"
    :header-info="[
        'logoSrc' => public_path('images/geraye_logo.jpeg'),
        'clinicName' => 'Geraye Home Care Services',
        'documentTitle' => 'Lead Source Details',
    ]"
    :footer-info="[
        'generatedDate' => true,
    ]"
/>
