<?php

namespace App\Http\Controllers\Insurance;

use App\Http\Controllers\Base\BaseController;
use App\Models\EmployeeInsuranceRecord;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Inertia\Inertia;
use App\Http\Requests\StoreEmployeeInsuranceRecordRequest;
use App\Http\Requests\UpdateEmployeeInsuranceRecordRequest;
use App\Services\Insurance\EmployeeInsuranceRecordService;

class EmployeeInsuranceRecordController extends BaseController
{
    public function __construct(EmployeeInsuranceRecordService $employeeInsuranceRecordService)
    {
        parent::__construct(
            $employeeInsuranceRecordService,
            EmployeeInsuranceRecordRules::class,
            'Insurance/EmployeeInsuranceRecords',
            'employeeInsuranceRecords',
            EmployeeInsuranceRecord::class,
            CreateEmployeeInsuranceRecordDTO::class
        );
    }

}
