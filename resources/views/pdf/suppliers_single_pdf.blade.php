<x-printable-report
    title="Supplier Details"
    :data="[
        ['label' => 'Name', 'value' => $supplier->name],
        ['label' => 'Contact Person', 'value' => $supplier->contact_person ?? '-'],
        ['label' => 'Email', 'value' => $supplier->email ?? '-'],
        ['label' => 'Phone', 'value' => $supplier->phone ?? '-'],
        ['label' => 'Address', 'value' => $supplier->address ?? '-'],
    ]"
    :columns="[
        ['key' => 'label', 'label' => 'Field', 'printWidth' => '30%'],
        ['key' => 'value', 'label' => 'Value', 'printWidth' => '70%'],
    ]"
    :header-info="[
        'logoSrc' => public_path('images/geraye_logo.jpeg'),
        'clinicName' => 'Geraye Home Care Services',
        'documentTitle' => 'Supplier Details Report',
    ]"
    :footer-info="[
        'generatedDate' => true,
    ]"
/>