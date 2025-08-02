<x-printable-report
    title="Insurance Policy - {{ $insurancePolicy->service_type }}"
    :data="[
        ['label' => 'Service Type', 'value' => $insurancePolicy->service_type],
        ['label' => 'Service Type (Amharic)', 'value' => $insurancePolicy->service_type_amharic],
        ['label' => 'Coverage Percentage', 'value' => $insurancePolicy->coverage_percentage . '%'],
        ['label' => 'Coverage Type', 'value' => $insurancePolicy->coverage_type],
        ['label' => 'Is Active', 'value' => $insurancePolicy->is_active ? 'Yes' : 'No'],
        ['label' => 'Notes', 'value' => $insurancePolicy->notes],
        ['label' => 'Created At', 'value' => $insurancePolicy->created_at],
        ['label' => 'Updated At', 'value' => $insurancePolicy->updated_at],
    ]"
    :columns="[
        ['key' => 'label', 'label' => 'Field', 'printWidth' => '30%'],
        ['key' => 'value', 'label' => 'Value', 'printWidth' => '70%'],
    ]"
    :header-info="[
        'logoSrc' => public_path('images/geraye_logo.jpeg'),
        'clinicName' => 'Geraye Home Care Services',
        'documentTitle' => 'Insurance Policy Details',
    ]"
    :footer-info="[
        'generatedDate' => true,
    ]"
/>