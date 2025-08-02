<x-printable-report
    title="Patient Export - Geraye Home Care Services"
    :data="$patients->map(function($p, $index) {
        return [
            'index' => $index + 1,
            'full_name' => $p->full_name,
            'patient_code' => $p->patient_code ?? '-',
            'source' => $p->source ?? '-',
            'phone_number' => $p->phone_number,
            'address' => $p->address ?? '-',
            'gender' => $p->gender,
            'emergency_contact' => $p->emergency_contact,
        ];
    })->toArray()"
    :columns="[
        ['key' => 'index', 'label' => '#'],
        ['key' => 'full_name', 'label' => 'Full Name'],
        ['key' => 'patient_code', 'label' => 'Patient Code'],
        ['key' => 'source', 'label' => 'Source'],
        ['key' => 'phone_number', 'label' => 'Phone'],
        ['key' => 'address', 'label' => 'Address'],
        ['key' => 'gender', 'label' => 'Gender'],
        ['key' => 'emergency_contact', 'label' => 'Emergency Contact'],
    ]"
    :header-info="[
        'logoSrc' => public_path('images/geraye_logo.jpeg'),
        'clinicName' => 'Geraye Home Care Services',
        'documentTitle' => 'Patient Records Export',
    ]"
    :footer-info="[
        'generatedDate' => true,
    ]"
/>