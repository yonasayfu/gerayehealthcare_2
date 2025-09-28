<?php

namespace App\Exports;

use App\Models\MarketingTask;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MarketingTasksExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping
{
    public function collection()
    {
        return MarketingTask::with(['campaign', 'assignedToStaff', 'relatedContent', 'doctor'])->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Task Code',
            'Title',
            'Description',
            'Task Type',
            'Status',
            'Scheduled At',
            'Completed At',
            'Notes',
            'Campaign',
            'Assigned To Staff',
            'Related Content',
            'Doctor',
            'Created At',
            'Updated At',
        ];
    }

    public function map($task): array
    {
        return [
            $task->id,
            $task->task_code,
            $task->title,
            $task->description,
            $task->task_type,
            $task->status,
            $task->scheduled_at,
            $task->completed_at,
            $task->notes,
            $task->campaign->campaign_name ?? '-',
            $task->assignedToStaff->full_name ?? '-',
            $task->relatedContent->title ?? '-',
            $task->doctor->full_name ?? '-',
            $task->created_at,
            $task->updated_at,
        ];
    }
}
