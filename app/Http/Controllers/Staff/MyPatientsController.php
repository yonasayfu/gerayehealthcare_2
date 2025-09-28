<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\VisitService;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class MyPatientsController extends Controller
{
    public function index()
    {
        $staffId = optional(Auth::user()->staff)->id;
        if (! $staffId) {
            return redirect()->route('dashboard')
                ->with('banner', 'Your account is not linked to a staff profile.')
                ->with('bannerStyle', 'danger');
        }

        $patientStats = VisitService::with('patient')
            ->where('staff_id', $staffId)
            ->get()
            ->groupBy('patient_id')
            ->map(function ($visits) {
                $p = $visits->first()->patient;
                return [
                    'id' => $p?->id,
                    'name' => $p?->full_name ?? 'Unknown Patient',
                    'visit_count' => $visits->count(),
                    'last_visit' => optional($visits->sortByDesc('scheduled_at')->first())->scheduled_at,
                ];
            })
            ->values();

        return Inertia::render('Staff/MyPatients/Index', [
            'patients' => $patientStats,
        ]);
    }

    public function show(int $patientId)
    {
        $staffId = optional(Auth::user()->staff)->id;
        if (! $staffId) {
            return redirect()->route('dashboard')
                ->with('banner', 'Your account is not linked to a staff profile.')
                ->with('bannerStyle', 'danger');
        }

        // Ensure this patient has at least one visit with current staff
        $visits = VisitService::with('patient')
            ->where('staff_id', $staffId)
            ->where('patient_id', $patientId)
            ->orderByDesc('scheduled_at')
            ->get();

        abort_if($visits->isEmpty(), 403, 'Unauthorized patient record.');

        $patient = $visits->first()->patient;

        return Inertia::render('Staff/MyPatients/Show', [
            'patient' => [
                'id' => $patient->id,
                'full_name' => $patient->full_name,
            ],
            'visits' => $visits->map(function ($v) {
                return [
                    'id' => $v->id,
                    'scheduled_at' => (string) $v->scheduled_at,
                    'status' => $v->status,
                    'check_in_time' => (string) $v->check_in_time,
                    'check_out_time' => (string) $v->check_out_time,
                    'notes' => $v->visit_notes,
                ];
            }),
        ]);
    }
}
