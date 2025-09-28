<?php

namespace App\Services\InventoryMaintenanceRecord;

use App\Models\InventoryMaintenanceRecord;
use App\Services\Base\BaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;

class InventoryMaintenanceRecordService extends BaseService
{
    public function __construct(InventoryMaintenanceRecord $inventoryMaintenanceRecord)
    {
        parent::__construct($inventoryMaintenanceRecord);
    }

    protected function applySearch($query, $search)
    {
        $query->where('description', 'like', "%{$search}%")
            ->orWhere('maintenance_date', 'like', "%{$search}%");
    }

    public function getAll(Request $request, array $with = [])
    {
        // Always eager load item for listings
        $with = array_unique(array_merge(['item'], $with));

        return parent::getAll($request, $with);
    }

    public function getById(int $id, array $with = [])
    {
        // Always eager load item for Show view and merge any additional relations
        $with = array_unique(array_merge(['item'], $with));

        return parent::getById($id, $with);
    }

    public function export()
    {
        $records = $this->model->all();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="inventory_maintenance_records_' . Str::slug(now()) . '.csv"',
        ];

        $callback = function() use ($records) {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                'ID',
                'Item ID',
                'Scheduled Date',
                'Actual Date',
                'Cost',
                'Description',
                'Next Due Date',
                'Status',
                'Created At',
                'Updated At',
                'Performed By Staff ID',
            ]);

            foreach ($records as $record) {
                fputcsv($file, [
                    $record->id,
                    $record->item_id,
                    $record->scheduled_date,
                    $record->actual_date,
                    $record->cost,
                    $record->description,
                    $record->next_due_date,
                    $record->status,
                    $record->created_at,
                    $record->updated_at,
                    $record->performed_by_staff_id,
                ]);
            }
            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }
}
