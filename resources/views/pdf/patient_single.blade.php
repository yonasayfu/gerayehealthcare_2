<x-printable-report
    title="Patient Record - {{ $patient->full_name }}"
    :data="[
        ['label' => 'Full Name', 'value' => $patient->full_name],
        ['label' => 'Patient Code', 'value' => $patient->patient_code ?? '-'],
        ['label' => 'Fayda ID', 'value' => $patient->fayda_id ?? '-'],
        ['label' => 'Gender', 'value' => $patient->gender ?? '-'],
        ['label' => 'Date of Birth', 'value' => $patient->date_of_birth ? \Carbon\Carbon::parse($patient->date_of_birth)->format('M d, Y') : '-'],
        ['label' => 'Age', 'value' => $patient->age !== null ? $patient->age : '-'],
        ['label' => 'Phone Number', 'value' => $patient->phone_number ?? '-'],
        ['label' => 'Email', 'value' => $patient->email ?? '-'],
        ['label' => 'Emergency Contact', 'value' => $patient->emergency_contact ?? '-'],
        ['label' => 'Address', 'value' => $patient->address ?? '-'],
        ['label' => 'Source', 'value' => $patient->source ?? '-'],
        ['label' => 'Geolocation', 'value' => $patient->geolocation ?? '-'],
        ['label' => 'Registered By', 'value' => $patient->registeredByStaff->full_name ?? '-'],
        ['label' => 'Registered Date', 'value' => $patient->created_at ? \Carbon\Carbon::parse($patient->created_at)->format('M d, Y H:i') : '-'],
        ['label' => 'Last Updated', 'value' => $patient->updated_at ? \Carbon\Carbon::parse($patient->updated_at)->format('M d, Y H:i') : '-'],
    ]"
    :columns="[
        ['key' => 'label', 'label' => 'Field', 'printWidth' => '30%'],
        ['key' => 'value', 'label' => 'Value', 'printWidth' => '70%'],
    ]"
    :header-info="[
        'logoSrc' => public_path('images/geraye_logo.jpeg'),
        'clinicName' => 'Geraye Home Care Services',
        'documentTitle' => 'Patient Record',
    ]"
    :footer-info="[
        'generatedDate' => true,
    ]"
/>