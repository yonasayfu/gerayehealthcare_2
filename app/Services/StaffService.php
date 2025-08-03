<?php

namespace App\Services;

use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Traits\ExportableTrait;
use App\Http\Config\ExportConfig;

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

    public function create(array $data): Staff
    {
        if (isset($data['photo'])) {
            $data['photo'] = $data['photo']->store('images/staff', 'public');
        }

        return parent::create($data);
    }

    public function update(int $id, array $data): Staff
    {
        $staff = $this->getById($id);

        if (isset($data['photo'])) {
            if ($staff->photo && Storage::disk('public')->exists($staff->photo)) {
                Storage::disk('public')->delete($staff->photo);
            }
            $data['photo'] = $data['photo']->store('images/staff', 'public');
        }

        return parent::update($id, $data);
    }

    public function export(Request $request)
    {
        return $this->handleExport($request, Staff::class, ExportConfig::getStaffConfig());
    }
}
