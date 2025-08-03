<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Base\BaseController;
use App\Services\PatientService;
use App\Models\Patient;
use App\Services\Validation\Rules\PatientRules;

class PatientController extends BaseController
{
    public function __construct(PatientService $patientService)
    {
        parent::__construct(
            $patientService,
            PatientRules::class,
            'Admin/Patients',
            'patients',
            Patient::class
        );
    }

    public function show(Patient $patient)
    {
        return parent::show($patient->id);
    }

    public function edit(Patient $patient)
    {
        return parent::edit($patient->id);
    }

    public function update(Request $request, Patient $patient)
    {
        return parent::update($request, $patient->id);
    }

    public function destroy(Patient $patient)
    {
        return parent::destroy($patient->id);
    }

    public function printSingle(Patient $patient)
    {
        return parent::printSingle($patient->id);
    }
}
