<?php

namespace App\Services;

use App\Models\Supplier;
use Illuminate\Http\Request;
use App\Http\Traits\ExportableTrait;
use App\Http\Config\AdditionalExportConfigs;
use Illuminate\Support\Facades\Validator;

class SupplierService extends BaseService
{
    use ExportableTrait;

    public function __construct(Supplier $supplier)
    {
        parent::__construct($supplier);
    }

    protected function applySearch($query, $search)
    {
        $query->where('name', 'like', "%{$search}%")
              ->orWhere('contact_person', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%");
    }

    public function import(Request $request): array
    {
        $file = $request->file('csv_file');
        $path = $file->getRealPath();
        $records = array_map('str_getcsv', file($path));
        $header = array_shift($records);

        $rules = [
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string',
        ];

        $errors = [];
        foreach ($records as $index => $row) {
            $data = array_combine($header, $row);
            $validator = Validator::make($data, $rules);

            if ($validator->fails()) {
                $errors["Row " . ($index + 2)] = $validator->errors()->all();
            } else {
                $this->create($data);
            }
        }

        return $errors;
    }

    public function create(array|object $data): Supplier
    {
        $data = is_object($data) ? (array) $data : $data;
        return parent::create($data);
    }

    public function export(Request $request)
    {
        return $this->handleExport($request, Supplier::class, AdditionalExportConfigs::getSupplierConfig());
    }

    public function printSingle($id)
    {
        $supplier = $this->getById($id);
        return $this->generateSingleRecordPdf($supplier, AdditionalExportConfigs::getSupplierConfig()['single_record']);
    }

    public function printCurrent(Request $request)
    {
        return $this->handlePrintCurrent($request, Supplier::class, AdditionalExportConfigs::getSupplierConfig());
    }

    public function printAll(Request $request)
    {
        return $this->handlePrintAll($request, Supplier::class, AdditionalExportConfigs::getSupplierConfig());
    }
}
