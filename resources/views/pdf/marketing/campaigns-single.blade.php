<x-printable-report
    title="Marketing Campaign Details - {{ $campaign->campaign_name }}"
    :data="[
        ['label' => 'Campaign Name', 'value' => $campaign->campaign_name],
        ['label' => 'Campaign Code', 'value' => $campaign->campaign_code ?? '-'],
        ['label' => 'Platform', 'value' => $campaign->platform->name ?? '-'],
        ['label' => 'Campaign Type', 'value' => $campaign->campaign_type ?? '-'],
        ['label' => 'Status', 'value' => $campaign->status ?? '-'],
        ['label' => 'Start Date', 'value' => $campaign->start_date ?? '-'],
        ['label' => 'End Date', 'value' => $campaign->end_date ?? '-'],
        ['label' => 'Budget Allocated', 'value' => number_format($campaign->budget_allocated ?? 0, 2)],
        ['label' => 'Budget Spent', 'value' => number_format($campaign->budget_spent ?? 0, 2)],
        ['label' => 'UTM Campaign', 'value' => $campaign->utm_campaign ?? '-'],
        ['label' => 'UTM Source', 'value' => $campaign->utm_source ?? '-'],
        ['label' => 'UTM Medium', 'value' => $campaign->utm_medium ?? '-'],
        ['label' => 'Assigned Staff', 'value' => $campaign->assignedStaff->full_name ?? '-'],
        ['label' => 'Created By', 'value' => $campaign->createdByStaff->full_name ?? '-'],
        ['label' => 'Created At', 'value' => \Carbon\Carbon::parse($campaign->created_at)->format('F j, Y, g:i a')],
        ['label' => 'Updated At', 'value' => \Carbon\Carbon::parse($campaign->updated_at)->format('F j, Y, g:i a')],
        ['label' => 'Target Audience', 'value' => json_encode($campaign->target_audience, JSON_PRETTY_PRINT)],
        ['label' => 'Goals', 'value' => json_encode($campaign->goals, JSON_PRETTY_PRINT)],
    ]"
    :columns="[
        ['key' => 'label', 'label' => 'Field', 'printWidth' => '30%'],
        ['key' => 'value', 'label' => 'Value', 'printWidth' => '70%'],
    ]"
    :header-info="[
        'logoSrc' => public_path('images/geraye_logo.jpeg'),
        'clinicName' => 'Geraye Home Care Services',
        'documentTitle' => 'Marketing Campaign Details',
    ]"
    :footer-info="[
        'generatedDate' => true,
    ]"
/>