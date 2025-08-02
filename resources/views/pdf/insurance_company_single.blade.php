<x-printable-report
    title="Insurance Company - {{ $insuranceCompany->name }}"
    :data="[
        ['label' => 'Name', 'value' => $insuranceCompany->name],
        ['label' => 'Name (Amharic)', 'value' => $insuranceCompany->name_amharic],
        ['label' => 'Contact Person', 'value' => $insuranceCompany->contact_person],
        ['label' => 'Contact Email', 'value' => $insuranceCompany->contact_email],
        ['label' => 'Contact Phone', 'value' => $insuranceCompany->contact_phone],
        ['label' => 'Address', 'value' => $insuranceCompany->address],
        ['label' => 'Address (Amharic)', 'value' => $insuranceCompany->address_amharic],
    ]"
    :columns="[
        ['key' => 'label', 'label' => 'Field', 'printWidth' => '30%'],
        ['key' => 'value', 'label' => 'Value', 'printWidth' => '70%'],
    ]"
    :header-info="[
        'logoSrc' => public_path('images/geraye_logo.jpeg'),
        'clinicName' => 'Geraye Home Care Services',
        'documentTitle' => 'Insurance Company Details',
    ]"
    :footer-info="[
        'generatedDate' => true,
    ]"
/>