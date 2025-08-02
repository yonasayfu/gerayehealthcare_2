<x-printable-report
    title="Marketing Leads List"
    :data="$leads->map(function($lead) {
        return [
            'lead_code' => $lead->lead_code,
            'name' => $lead->first_name . ' ' . $lead->last_name,
            'email' => $lead->email,
            'phone' => $lead->phone_number,
            'status' => $lead->status,
            'source_campaign' => $lead->sourceCampaign->name ?? 'N/A',
            'landing_page' => $lead->landingPage->name ?? 'N/A',
            'assigned_staff' => $lead->assignedStaff->user->name ?? 'N/A',
        ];
    })->toArray()"
    :columns="[
        ['key' => 'lead_code', 'label' => 'Lead Code'],
        ['key' => 'name', 'label' => 'Name'],
        ['key' => 'email', 'label' => 'Email'],
        ['key' => 'phone', 'label' => 'Phone'],
        ['key' => 'status', 'label' => 'Status'],
        ['key' => 'source_campaign', 'label' => 'Source Campaign'],
        ['key' => 'landing_page', 'label' => 'Landing Page'],
        ['key' => 'assigned_staff', 'label' => 'Assigned Staff'],
    ]"
    :header-info="[
        'logoSrc' => public_path('images/geraye_logo.jpeg'),
        'clinicName' => 'Geraye Home Care Services',
        'documentTitle' => 'Marketing Leads List',
    ]"
    :footer-info="[
        'generatedDate' => true,
    ]"
/>
