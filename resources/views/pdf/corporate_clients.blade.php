<x-printable-report
    title="Corporate Clients List - Geraye Home Care Services"
    :data="$corporateClients->map(function($client) {
        return [
            'organization_name' => $client->organization_name,
            'contact_person' => $client->contact_person,
            'contact_email' => $client->contact_email,
            'contact_phone' => $client->contact_phone,
            'tin_number' => $client->tin_number,
            'trade_license_number' => $client->trade_license_number,
            'address' => $client->address,
        ];
    })->toArray()"
    :columns="[
        ['key' => 'organization_name', 'label' => 'Organization Name'],
        ['key' => 'contact_person', 'label' => 'Contact Person'],
        ['key' => 'contact_email', 'label' => 'Contact Email'],
        ['key' => 'contact_phone', 'label' => 'Contact Phone'],
        ['key' => 'tin_number', 'label' => 'TIN Number'],
        ['key' => 'trade_license_number', 'label' => 'Trade License Number'],
        ['key' => 'address', 'label' => 'Address'],
    ]"
    :header-info="[
        'logoSrc' => public_path('images/geraye_logo.jpeg'),
        'clinicName' => 'Geraye Home Care Services',
        'documentTitle' => 'Corporate Clients List',
    ]"
    :footer-info="[
        'generatedDate' => true,
    ]"
/>