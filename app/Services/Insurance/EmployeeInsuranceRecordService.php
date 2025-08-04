<?php

namespace App\Services\Insurance;

use App\Models\EmployeeInsuranceRecord;
use App\DTOs\CreateEmployeeInsuranceRecordDTO;
use App\Services\BaseService;

class EmployeeInsuranceRecordService extends BaseService
{
    public function __construct(EmployeeInsuranceRecord $employeeInsuranceRecord)
    {
        parent::__construct($employeeInsuranceRecord);
    }

    public function create(array $data): EmployeeInsuranceRecord
    {
        return parent::create($data);
    }
}
