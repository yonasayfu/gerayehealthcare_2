<?php

namespace App\Exports;

use App\Models\LeadSource;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class LeadSourcesExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    public function collection()
    {
        return LeadSource::all();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Category',
            'Description',
            'Is Active',
            'Created At',
            'Updated At',
        ];
    }

    public function map($source): array
    {
        return [
            $source->id,
            $source->name,
            $source->category,
            $source->description,
            $source->is_active ? 'Yes' : 'No',
            $source->created_at,
            $source->updated_at,
        ];
    }
}
