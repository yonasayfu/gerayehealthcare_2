<x-printable-report
    title="Staff Export - Geraye Home Care Services"
    :data="$staff->map(function($s, $index) {
        return [
            'index' => $index + 1,
            'full_name' => $s->first_name . ' ' . $s->last_name,
            'email' => $s->email ?? '-',
            'phone' => $s->phone ?? '-',
            'position' => $s->position ?? '-',
            'department' => $s->department ?? '-',
            'status' => $s->status,
            'hire_date' => \Carbon\Carbon::parse($s->hire_date)->format('Y-m-d'),
        ];
    })->toArray()"
    :columns="[
        ['key' => 'index', 'label' => '#'],
        ['key' => 'full_name', 'label' => 'Full Name'],
        ['key' => 'email', 'label' => 'Email'],
        ['key' => 'phone', 'label' => 'Phone'],
        ['key' => 'position', 'label' => 'Position'],
        ['key' => 'department', 'label' => 'Department'],
        ['key' => 'status', 'label' => 'Status'],
        ['key' => 'hire_date', 'label' => 'Hire Date'],
    ]"
    :header-info="[
        'logoSrc' => public_path('images/geraye_logo.jpeg'),
        'clinicName' => 'Geraye Home Care Services',
        'documentTitle' => 'All Staff Records',
    ]"
    :footer-info="[
        'generatedDate' => true,
    ]"
/>