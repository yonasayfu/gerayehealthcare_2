<?php

namespace App\Services;

use App\Http\Config\ExportConfig;
use App\Http\Traits\ExportableTrait;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StaffService extends BaseService
{
    use ExportableTrait;

    public function __construct(Staff $staff)
    {
        parent::__construct($staff);
    }

    protected function applySearch($query, $search)
    {
        $query->where('first_name', 'ilike', "%{$search}%")
            ->orWhere('last_name', 'ilike', "%{$search}%")
            ->orWhere('email', 'ilike', "%{$search}%");
    }

    public function create(array|object $data): Staff
    {
        $data = is_object($data) ? (array) $data : $data;

        // Normalize hourly_rate if provided
        if (array_key_exists('hourly_rate', $data)) {
            if ($data['hourly_rate'] === '' || $data['hourly_rate'] === null) {
                $data['hourly_rate'] = null;
            } elseif (is_string($data['hourly_rate']) || is_numeric($data['hourly_rate'])) {
                $data['hourly_rate'] = (float) $data['hourly_rate'];
            }
        }

        if (isset($data['photo']) && $data['photo']) {
            $data['photo'] = $data['photo']->store('images/staff', 'public');
        } else {
            unset($data['photo']);
        }

        // Avoid sending nulls that can overwrite NOT NULL columns unintentionally
        $data = array_filter($data, fn ($v) => ! is_null($v));

        return parent::create($data);
    }

    public function update(int $id, array|object $data): Staff
    {
        $data = is_object($data) ? (array) $data : $data;

        $staff = $this->getById($id);

        // Normalize hourly_rate if present in payload
        if (array_key_exists('hourly_rate', $data)) {
            if ($data['hourly_rate'] === '' || $data['hourly_rate'] === null) {
                // To avoid overwriting with null unintentionally, remove the key when empty
                unset($data['hourly_rate']);
            } elseif (is_string($data['hourly_rate']) || is_numeric($data['hourly_rate'])) {
                $data['hourly_rate'] = (float) $data['hourly_rate'];
            }
        }

        if (isset($data['photo'])) {
            if ($staff->photo && Storage::disk('public')->exists($staff->photo)) {
                Storage::disk('public')->delete($staff->photo);
            }
            $data['photo'] = $data['photo']->store('images/staff', 'public');
        } else {
            unset($data['photo']);
        }

        // Avoid sending nulls that can overwrite NOT NULL columns unintentionally
        $data = array_filter($data, fn ($v) => $v !== null);

        return parent::update($id, $data);
    }

    public function export(Request $request)
    {
        return $this->handleExport($request, Staff::class, ExportConfig::getStaffConfig());
    }

    public function printAll(Request $request)
    {
        return $this->handlePrintAll($request, Staff::class, ExportConfig::getStaffConfig());
    }

    public function printCurrent(Request $request)
    {
        return $this->handlePrintCurrent($request, Staff::class, ExportConfig::getStaffConfig());
    }

    public function printSingle(Staff $staff, Request $request)
    {
        return $this->handlePrintSingle($request, $staff, ExportConfig::getStaffConfig());
    }
}
