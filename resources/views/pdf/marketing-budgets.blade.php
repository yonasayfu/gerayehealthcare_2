<x-printable-report
    title="Marketing Budgets List"
    :data="$marketingBudgets->map(function($budget) {
        return [
            'budget_name' => $budget->budget_name,
            'campaign_name' => $budget->campaign->campaign_name ?? '-',
            'platform_name' => $budget->platform->name ?? '-',
            'allocated_amount' => $budget->allocated_amount,
            'spent_amount' => $budget->spent_amount,
            'period_start' => \Carbon\Carbon::parse($budget->period_start)->format('M d, Y'),
            'period_end' => $budget->period_end ? \Carbon\Carbon::parse($budget->period_end)->format('M d, Y') : '-',
            'status' => $budget->status,
        ];
    })->toArray()"
    :columns="[
        ['key' => 'budget_name', 'label' => 'Budget Name'],
        ['key' => 'campaign_name', 'label' => 'Campaign'],
        ['key' => 'platform_name', 'label' => 'Platform'],
        ['key' => 'allocated_amount', 'label' => 'Allocated'],
        ['key' => 'spent_amount', 'label' => 'Spent'],
        ['key' => 'period_start', 'label' => 'Start Date'],
        ['key' => 'period_end', 'label' => 'End Date'],
        ['key' => 'status', 'label' => 'Status'],
    ]"
    :header-info="[
        'logoSrc' => public_path('images/geraye_logo.jpeg'),
        'clinicName' => 'Geraye Home Care Services',
        'documentTitle' => 'Marketing Budgets List',
    ]"
    :footer-info="[
        'generatedDate' => true,
    ]"
/>