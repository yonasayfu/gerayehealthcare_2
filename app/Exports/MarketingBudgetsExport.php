<?php

namespace App\Exports;

use App\Models\MarketingBudget;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MarketingBudgetsExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping
{
    public function collection()
    {
        return MarketingBudget::with(['campaign', 'platform'])->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Budget Name',
            'Campaign',
            'Platform',
            'Description',
            'Allocated Amount',
            'Spent Amount',
            'Period Start',
            'Period End',
            'Status',
            'Created At',
            'Updated At',
        ];
    }

    public function map($budget): array
    {
        return [
            $budget->id,
            $budget->budget_name,
            $budget->campaign->campaign_name ?? '-',
            $budget->platform->name ?? '-',
            $budget->description,
            $budget->allocated_amount,
            $budget->spent_amount,
            $budget->period_start,
            $budget->period_end,
            $budget->status,
            $budget->created_at,
            $budget->updated_at,
        ];
    }
}
