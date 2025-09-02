<?php

namespace App\Exports;

use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StaffExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithStyles
{
    protected Collection $staff;

    public function __construct(Collection $staff)
    {
        $this->staff = $staff;
    }

    /**
     * Return the collection of staff to export
     */
    public function collection()
    {
        return $this->staff;
    }

    /**
     * Define the headings for the export
     */
    public function headings(): array
    {
        return [
            'Employee ID',
            'First Name',
            'Last Name',
            'Email',
            'Phone Number',
            'Position',
            'Department',
            'Hire Date',
            'Salary',
            'Employment Type',
            'Status',
            'Years of Service',
            'Emergency Contact',
            'Emergency Phone',
            'Address',
            'Notes',
            'Created At',
        ];
    }

    /**
     * Map the data for each row
     */
    public function map($staff): array
    {
        return [
            $staff->employee_id,
            $staff->first_name,
            $staff->last_name,
            $staff->email,
            $staff->phone_number,
            $staff->position,
            $staff->department,
            $staff->hire_date ? $staff->hire_date->format('Y-m-d') : '',
            $staff->salary ? '$'.number_format($staff->salary, 2) : '',
            $staff->employment_type_display,
            $staff->status_display,
            $staff->years_of_service,
            $staff->emergency_contact_name,
            $staff->emergency_contact_phone,
            $staff->address,
            $staff->notes,
            $staff->created_at ? $staff->created_at->format('Y-m-d H:i:s') : '',
        ];
    }

    /**
     * Apply styles to the worksheet
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold
            1 => ['font' => ['bold' => true]],
        ];
    }
}
