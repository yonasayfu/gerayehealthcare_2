<x-printable-report
    title="Patient Record - {{ $patient->full_name }}"
    :data="[
        ['label' => 'Fayda ID', 'value' => $patient->fayda_id ?? 'N/A'],
        ['label' => 'Source', 'value' => $patient->source ?? 'N/A'],
        ['label' => 'Phone Number', 'value' => $patient->phone_number ?? 'N/A'],
        ['label' => 'Date of Birth', 'value' => $patient->date_of_birth ? \Carbon\Carbon::parse($patient->date_of_birth)->format('F j, Y') : 'N/A'],
        ['label' => 'Gender', 'value' => $patient->gender ?? 'N/A'],
        ['label' => 'Emergency Contact', 'value' => $patient->emergency_contact ?? 'N/A'],
        ['label' => 'Address', 'value' => $patient->address ?? 'N/A'],
        ['label' => 'Geolocation', 'value' => $patient->geolocation ?? 'N/A'],
    ]"
    :columns="[
        ['key' => 'label', 'label' => 'Field', 'printWidth' => '30%'],
        ['key' => 'value', 'label' => 'Value', 'printWidth' => '70%'],
    ]"
    :header-info="[
        'logoSrc' => public_path('images/geraye_logo.jpeg'),
        'clinicName' => 'Geraye Home Care Services',
        'documentTitle' => 'Patient Record Card',
    ]"
    :footer-info="[
        'generatedDate' => true,
    ]"
/>