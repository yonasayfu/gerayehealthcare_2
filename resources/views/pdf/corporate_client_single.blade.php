<x-printable-report
    title="Corporate Client - {{ $corporateClient->organization_name }}"
    :data="[
        ['label' => 'Organization Name', 'value' => $corporateClient->organization_name],
        ['label' => 'Organization Name (Amharic)', 'value' => $corporateClient->organization_name_amharic],
        ['label' => 'Contact Person', 'value' => $corporateClient->contact_person],
        ['label' => 'Contact Email', 'value' => $corporateClient->contact_email],
        ['label' => 'Contact Phone', 'value' => $corporateClient->contact_phone],
        ['label' => 'TIN Number', 'value' => $corporateClient->tin_number],
        ['label' => 'Trade License Number', 'value' => $corporateClient->trade_license_number],
        ['label' => 'Address', 'value' => $corporateClient->address],
    ]"
    :columns="[
        ['key' => 'label', 'label' => 'Field', 'printWidth' => '30%'],
        ['key' => 'value', 'label' => 'Value', 'printWidth' => '70%'],
    ]"
    :header-info="[
        'logoSrc' => public_path('images/geraye_logo.jpeg'),
        'clinicName' => 'Geraye Home Care Services',
        'documentTitle' => 'Corporate Client Details',
    ]"
    :footer-info="[
        'generatedDate' => true,
    ]"
/>