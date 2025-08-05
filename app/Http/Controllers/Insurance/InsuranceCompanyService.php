<?php

namespace App\Http\Controllers\Insurance;

use App\Http\Controllers\Controller;
use App\Models\InsuranceCompany;
use Illuminate\Http\Request;

class InsuranceCompanyService extends Controller
{
    public function getAll()
    {
        return InsuranceCompany::all();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        return InsuranceCompany::create($data);
    }

    public function show(InsuranceCompany $insuranceCompany)
    {
        return $insuranceCompany;
    }

    public function update(Request $request, InsuranceCompany $insuranceCompany)
    {
        $data = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        $insuranceCompany->update($data);

        return $insuranceCompany;
    }

    public function destroy(InsuranceCompany $insuranceCompany)
    {
        $insuranceCompany->delete();

        return response()->json(null, 204);
    }
}
