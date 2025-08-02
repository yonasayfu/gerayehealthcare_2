<x-printable-report
    title="Marketing Campaigns List - Geraye Home Care Services"
    :data="$campaigns->map(function($campaign, $index) {
        return [
            'index' => $index + 1,
            'campaign_name' => $campaign->campaign_name,
            'campaign_code' => $campaign->campaign_code ?? '-',
            'platform_name' => $campaign->platform->name ?? '-',
            'campaign_type' => $campaign->campaign_type ?? '-',
            'status' => $campaign->status ?? '-',
            'start_date' => $campaign->start_date ?? '-',
            'end_date' => $campaign->end_date ?? '-',
            'budget_allocated' => number_format($campaign->budget_allocated ?? 0, 2),
            'budget_spent' => number_format($campaign->budget_spent ?? 0, 2),
            'assigned_staff' => $campaign->assignedStaff->full_name ?? '-',
            'created_by' => $campaign->createdByStaff->full_name ?? '-',
        ];
    })->toArray()"
    :columns="[
        ['key' => 'index', 'label' => '#'],
        ['key' => 'campaign_name', 'label' => 'Campaign Name'],
        ['key' => 'campaign_code', 'label' => 'Code'],
        ['key' => 'platform_name', 'label' => 'Platform'],
        ['key' => 'campaign_type', 'label' => 'Type'],
        ['key' => 'status', 'label' => 'Status'],
        ['key' => 'start_date', 'label' => 'Start Date'],
        ['key' => 'end_date', 'label' => 'End Date'],
        ['key' => 'budget_allocated', 'label' => 'Budget Allocated'],
        ['key' => 'budget_spent', 'label' => 'Budget Spent'],
        ['key' => 'assigned_staff', 'label' => 'Assigned Staff'],
        ['key' => 'created_by', 'label' => 'Created By'],
    ]"
    :header-info="[
        'logoSrc' => public_path('images/geraye_logo.jpeg'),
        'clinicName' => 'Geraye Home Care Services',
        'documentTitle' => 'Marketing Campaigns List',
    ]"
    :footer-info="[
        'generatedDate' => true,
    ]"
/>