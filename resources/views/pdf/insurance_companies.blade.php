<x-printable-report
    title="Insurance Companies List - Geraye Home Care Services"
    :data="$insuranceCompanies->map(function($company) {
        return [
            'name' => $company->name,
            'contact_person' => $company->contact_person,
            'contact_email' => $company->contact_email,
            'contact_phone' => $company->contact_phone,
            'address' => $company->address,
        ];
    })->toArray()"
    :columns="[
        ['key' => 'name', 'label' => 'Name'],
        ['key' => 'contact_person', 'label' => 'Contact Person'],
        ['key' => 'contact_email', 'label' => 'Contact Email'],
        ['key' => 'contact_phone', 'label' => 'Contact Phone'],
        ['key' => 'address', 'label' => 'Address'],
    ]"
    :header-info="[
        'logoSrc' => public_path('images/geraye_logo.jpeg'),
        'clinicName' => 'Geraye Home Care Services',
        'documentTitle' => 'Insurance Companies List',
    ]"
    :footer-info="[
        'generatedDate' => true,
    ]"
/>