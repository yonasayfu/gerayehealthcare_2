<?php

namespace App\Services;

use App\Models\Patient;
use App\Models\Staff;
use Illuminate\Support\Collection;

class GlobalSearchService
{
    /**
     * Perform simple global search - patients and staff only
     */
    public function search(string $query): array
    {
        if (empty($query) || strlen($query) < 2) {
            return [];
        }

        $results = collect();

        // Core search functionality - confirmed working
        $results = $results->merge($this->searchPatients($query));
        $results = $results->merge($this->searchStaff($query));

        // Return simple array of results
        return $results->take(10)->values()->toArray();
    }

    // Simple search methods - confirmed working
    private function searchPatients(string $query): Collection
    {
        $patients = Patient::select('id', 'full_name', 'patient_code', 'phone_number', 'email')
            ->where(function ($q) use ($query) {
                $q->where('full_name', 'ILIKE', '%'.$query.'%')
                    ->orWhere('patient_code', 'ILIKE', '%'.$query.'%')
                    ->orWhere('phone_number', 'ILIKE', '%'.$query.'%')
                    ->orWhere('email', 'ILIKE', '%'.$query.'%');
            })
            ->limit(5)
            ->get();

        return $patients->map(function ($patient) {
            return [
                'type' => 'Patient',
                'category' => 'Healthcare',
                'title' => $patient->full_name,
                'description' => 'Code: '.($patient->patient_code ?? 'N/A').' • '.($patient->phone_number ?? 'No phone'),
                'url' => route('admin.patients.show', $patient->id),
                'relevance' => 100,
                'icon' => 'user',
            ];
        });
    }

    private function searchStaff(string $query): Collection
    {
        $staff = Staff::select('id', 'first_name', 'last_name', 'email', 'position', 'role')
            ->where(function ($q) use ($query) {
                $q->where('first_name', 'ILIKE', '%'.$query.'%')
                    ->orWhere('last_name', 'ILIKE', '%'.$query.'%')
                    ->orWhere('email', 'ILIKE', '%'.$query.'%')
                    ->orWhere('position', 'ILIKE', '%'.$query.'%');
            })
            ->limit(5)
            ->get();

        return $staff->map(function ($s) {
            $fullName = trim($s->first_name.' '.$s->last_name);

            return [
                'type' => 'Staff',
                'category' => 'Healthcare',
                'title' => $fullName ?: 'Staff Member',
                'description' => ($s->position ?? $s->role ?? 'Staff').' • '.($s->email ?? 'No email'),
                'url' => route('admin.staff.show', $s->id),
                'relevance' => 90,
                'icon' => 'user-check',
            ];
        });
    }
}
