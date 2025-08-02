<x-printable-report
    title="Insurance Policies List - Geraye Home Care Services"
    :data="$insurancePolicies->map(function($policy) {
        return [
            'service_type' => $policy->service_type,
            'coverage_percentage' => $policy->coverage_percentage,
            'coverage_type' => $policy->coverage_type,
            'is_active' => $policy->is_active ? 'Yes' : 'No',
        ];
    })->toArray()"
    :columns="[
        ['key' => 'service_type', 'label' => 'Service Type'],
        ['key' => 'coverage_percentage', 'label' => 'Coverage Percentage'],
        ['key' => 'coverage_type', 'label' => 'Coverage Type'],
        ['key' => 'is_active', 'label' => 'Is Active'],
    ]"
    :header-info="[
        'logoSrc' => public_path('images/geraye_logo.jpeg'),
        'clinicName' => 'Geraye Home Care Services',
        'documentTitle' => 'Insurance Policies List',
    ]"
    :footer-info="[
        'generatedDate' => true,
    ]"
/>