<?php

namespace App\Services\Staff;

use App\Http\Config\ExportConfig;
use App\Http\Traits\ExportableTrait;
use App\Models\Staff;
use App\Services\Base\BaseService;
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

    public function getAll(Request $request, array $with = [])
    {
        $query = $this->model->with($with);

        if ($request->filled('search')) {
            $this->applySearch($query, $request->input('search'));
        }

        if ($request->filled('specialization') && $request->input('specialization') !== 'all') {
            $specialization = $request->input('specialization');
            $query->where(function ($q) use ($specialization) {
                $q->where('position', 'ilike', "%{$specialization}%")
                    ->orWhere('department', 'ilike', "%{$specialization}%");
            });
        }

        if ($request->filled('status') && $request->input('status') !== 'all') {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('availability_status') && $request->input('availability_status') !== 'all') {
            $availability = $request->input('availability_status');
            if ($availability === 'available') {
                $query->where('status', 'Active');
            } elseif ($availability === 'offline') {
                $query->where('status', 'Inactive');
            }
        }

        if ($request->filled('sort')) {
            $direction = $request->input('direction', 'asc');
            $query->orderBy($request->input('sort'), $direction);
        } else {
            $query->orderBy('last_name')->orderBy('first_name');
        }

        return $query->paginate($request->input('per_page', 20))->withQueryString();
    }

    public function create(array | object $data): Staff
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
        $data = array_filter($data, fn($v) => !is_null($v));

        return parent::create($data);
    }

    public function update(int $id, array | object $data): Staff
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
        $data = array_filter($data, fn($v) => $v !== null);

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

    public function delete(int $id): void
    {
        $staff = $this->getById($id);

        // Delete the photo if it exists
        if ($staff->photo && Storage::disk('public')->exists($staff->photo)) {
            Storage::disk('public')->delete($staff->photo);
        }

        parent::delete($id);
    }
}
