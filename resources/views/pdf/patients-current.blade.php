<x-printable-report
    title="Patient List (Current View) - Geraye Home Care Services"
    :data="$patients->map(function($p, $index) {
        return [
            'index' => $index + 1,
            'full_name' => $p->full_name,
            'patient_code' => $p->patient_code ?? '-',
            'fayda_id' => $p->fayda_id ?? '-',
            'age' => $p->date_of_birth ? \Carbon\Carbon::parse($p->date_of_birth)->age : '-',
            'gender' => $p->gender ?? '-',
            'phone_number' => $p->phone_number ?? '-',
            'source' => $p->source ?? '-',
        ];
    })->toArray()"
    :columns="[
        ['key' => 'index', 'label' => '#'],
        ['key' => 'full_name', 'label' => 'Full Name'],
        ['key' => 'patient_code', 'label' => 'Patient Code'],
        ['key' => 'fayda_id', 'label' => 'Fayda ID'],
        ['key' => 'age', 'label' => 'Age'],
        ['key' => 'gender', 'label' => 'Gender'],
        ['key' => 'phone_number', 'label' => 'Phone'],
        ['key' => 'source', 'label' => 'Source'],
    ]"
    :header-info="[
        'logoSrc' => public_path('images/geraye_logo.jpeg'),
        'clinicName' => 'Geraye Home Care Services',
        'documentTitle' => 'Patient List (Current View)',
    ]"
    :footer-info="[
        'generatedDate' => true,
    ]"
/>