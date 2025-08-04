<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Base\BaseController;
use App\Services\PatientService;
use App\Models\Patient;
use App\Services\Validation\Rules\PatientRules;
use App\DTOs\CreatePatientDTO;

class PatientController extends BaseController
{
    public function __construct(PatientService $patientService)
    {
        parent::__construct(
            $patientService,
            PatientRules::class,
            'Admin/Patients',
            'patients',
            Patient::class,
            CreatePatientDTO::class
        );
    }
}
