<x-printable-report
    title="Marketing Lead Details"
    :data="[
        ['label' => 'Lead Code', 'value' => $lead->lead_code],
        ['label' => 'Full Name', 'value' => $lead->first_name . ' ' . $lead->last_name],
        ['label' => 'Email', 'value' => $lead->email],
        ['label' => 'Phone Number', 'value' => $lead->phone_number],
        ['label' => 'Status', 'value' => $lead->status],
        ['label' => 'Source Campaign', 'value' => $lead->sourceCampaign->name ?? 'N/A'],
        ['label' => 'Landing Page', 'value' => $lead->landingPage->name ?? 'N/A'],
        ['label' => 'Assigned Staff', 'value' => $lead->assignedStaff->user->name ?? 'N/A'],
        ['label' => 'Conversion Date', 'value' => $lead->conversion_date ? \Carbon\Carbon::parse($lead->conversion_date)->format('M d, Y') : 'N/A'],
        ['label' => 'Converted Patient', 'value' => ($lead->convertedPatient->full_name ?? 'N/A') . ' (' . ($lead->convertedPatient->patient_code ?? 'N/A') . ')'],
        ['label' => 'Notes', 'value' => $lead->notes ?? 'N/A'],
        ['label' => 'Created At', 'value' => \Carbon\Carbon::parse($lead->created_at)->format('M d, Y H:i')],
        ['label' => 'Updated At', 'value' => \Carbon\Carbon::parse($lead->updated_at)->format('M d, Y H:i')],
    ]"
    :columns="[
        ['key' => 'label', 'label' => 'Field', 'printWidth' => '30%'],
        ['key' => 'value', 'label' => 'Value', 'printWidth' => '70%'],
    ]"
    :header-info="[
        'logoSrc' => public_path('images/geraye_logo.jpeg'),
        'clinicName' => 'Geraye Home Care Services',
        'documentTitle' => 'Marketing Lead Details',
    ]"
    :footer-info="[
        'generatedDate' => true,
    ]"
/>
