<x-printable-report
    title="Suppliers Report"
    :data="$suppliers->map(function($supplier) {
        return [
            'name' => $supplier->name,
            'contact_person' => $supplier->contact_person,
            'email' => $supplier->email,
            'phone' => $supplier->phone,
            'address' => $supplier->address,
        ];
    })->toArray()"
    :columns="[
        ['key' => 'name', 'label' => 'Name'],
        ['key' => 'contact_person', 'label' => 'Contact Person'],
        ['key' => 'email', 'label' => 'Email'],
        ['key' => 'phone', 'label' => 'Phone'],
        ['key' => 'address', 'label' => 'Address'],
    ]"
    :header-info="[
        'logoSrc' => public_path('images/geraye_logo.jpeg'),
        'clinicName' => 'Geraye Home Care Services',
        'documentTitle' => 'Suppliers Report',
    ]"
    :footer-info="[
        'generatedDate' => true,
    ]"
/>