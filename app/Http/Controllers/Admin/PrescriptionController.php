<?php

namespace App\Http\Controllers\Admin;

use App\Http\Config\AdditionalExportConfigs;
use App\Http\Controllers\Base\BaseController;
use App\Http\Traits\ExportableTrait;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\Staff;
use App\Services\Prescription\PrescriptionService;
use App\Services\Validation\Rules\PrescriptionRules;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class PrescriptionController extends BaseController
{
    use ExportableTrait;

    public function __construct(PrescriptionService $service)
    {
        parent::__construct(
            $service,
            PrescriptionRules::class,
            'Admin/Prescriptions',
            'prescriptions',
            Prescription::class
        );
    }

    public function create()
    {
        return inertia('Admin/Prescriptions/Create', [
            'patients' => Patient::select('id', 'full_name', 'patient_code')->orderBy('full_name')->get(),
            'staff' => Staff::select('id', 'first_name', 'last_name')->orderBy('first_name')->get(),
        ]);
    }

    public function edit($id)
    {
        $data = $this->service->getById($id);
        return inertia('Admin/Prescriptions/Edit', [
            'prescription' => $data,
            'patients' => Patient::select('id', 'full_name', 'patient_code')->orderBy('full_name')->get(),
            'staff' => Staff::select('id', 'first_name', 'last_name')->orderBy('first_name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        // Inject defaults
        $request->merge([
            'created_by_staff_id' => $request->input('created_by_staff_id') ?: optional(auth()->user()->staff)->id,
            'prescribed_date' => $request->input('prescribed_date') ?: Carbon::today()->toDateString(),
        ]);

        return parent::store($request);
    }

    public function update(Request $request, $id)
    {
        if (!$request->filled('created_by_staff_id') && optional(auth()->user()->staff)->id) {
            $request->merge(['created_by_staff_id' => auth()->user()->staff->id]);
        }
        return parent::update($request, $id);
    }

    public function export(Request $request)
    {
        $config = AdditionalExportConfigs::getPrescriptionConfig();

        return $this->handleExport($request, Prescription::class, $config);
    }

    public function printAll(Request $request)
    {
        $config = AdditionalExportConfigs::getPrescriptionConfig();

        return $this->handlePrintAll($request, Prescription::class, $config);
    }

    public function printCurrent(Request $request)
    {
        $config = AdditionalExportConfigs::getPrescriptionConfig();

        return $this->handlePrintCurrent($request, Prescription::class, $config);
    }

    public function printSingle(Request $request, $id)
    {
        $model = Prescription::with('items')->findOrFail($id);
        $config = AdditionalExportConfigs::getPrescriptionConfig();

        return $this->handlePrintSingle($request, $model, $config);
    }

    public function shareLink(Prescription $prescription)
    {
        $now = Carbon::now();
        if (!$prescription->share_token || ($prescription->share_expires_at && $prescription->share_expires_at->isPast())) {
            $prescription->share_token = Str::random(48);
            $prescription->share_expires_at = $now->copy()->addDays(30);
            $prescription->save();
        }
        $url = route('public.prescriptions.show', ['token' => $prescription->share_token]);
        return response()->json(['url' => $url, 'expires_at' => optional($prescription->share_expires_at)->toIso8601String()]);
    }

    public function shareViaEmail(Prescription $prescription, Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'message' => 'nullable|string|max:500',
        ]);

        // Ensure share link exists
        $shareData = $this->shareLink($prescription)->original;
        $shareUrl = $shareData['url'];

        // Get patient and doctor information
        $patientName = $prescription->patient ? $prescription->patient->full_name : 'Patient';
        $doctorName = $prescription->createdBy ?
        ($prescription->createdBy->first_name . ' ' . $prescription->createdBy->last_name) :
        'Doctor';
        $prescribedDate = $prescription->prescribed_date ?
        Carbon::parse($prescription->prescribed_date)->format('F j, Y') :
        'Not specified';
        $status = $prescription->status ?? 'draft';

        // Prepare email data
        $emailData = [
            'shareUrl' => $shareUrl,
            'patientName' => $patientName,
            'doctorName' => $doctorName,
            'prescribedDate' => $prescribedDate,
            'status' => $status,
            'messageText' => $request->input('message'),
        ];

        // Send professional email template
        Mail::send('emails.prescription-share', $emailData, function ($message) use ($request, $patientName, $doctorName) {
            $message->to($request->input('email'))
                ->subject("Prescription for {$patientName} from Dr. {$doctorName}");
        });

        return response()->json(['message' => 'Prescription shared via email successfully']);
    }

    public function rotateShareLink(Prescription $prescription)
    {
        $prescription->share_token = Str::random(48);
        $prescription->share_expires_at = Carbon::now()->addDays(30);
        $prescription->save();
        return response()->json(['ok' => true]);
    }

    public function expireShareLink(Prescription $prescription)
    {
        $prescription->share_expires_at = Carbon::now();
        $prescription->save();
        return response()->json(['ok' => true]);
    }

    public function setSharePin(Prescription $prescription, Request $request)
    {
        $request->validate(['pin' => 'nullable|string|max:20']);
        $prescription->share_pin = $request->input('pin');
        $prescription->save();
        return response()->json(['ok' => true]);
    }

    public function publicShow(string $token)
    {
        $rx = Prescription::with(['patient', 'createdBy', 'items'])
            ->where('share_token', $token)
            ->first();
        if (!$rx || ($rx->share_expires_at && $rx->share_expires_at->isPast())) {
            abort(404);
        }
        if ($rx->share_pin && !session()->has('rx_auth_' . $token)) {
            return view('public.prescription-pin', ['token' => $token]);
        }
        $rx->increment('share_views');
        $rx->last_viewed_at = now();
        $rx->save();
        return view('public.prescription', ['rx' => $rx]);
    }

    public function publicAuthenticate(Request $request, string $token)
    {
        $request->validate(['pin' => 'required|string|max:20']);
        $rx = Prescription::where('share_token', $token)->first();
        if (!$rx || ($rx->share_expires_at && $rx->share_expires_at->isPast())) {
            abort(404);
        }
        if (hash_equals((string) $rx->share_pin, (string) $request->input('pin'))) {
            session()->put('rx_auth_' . $token, true);
            return redirect()->route('public.prescriptions.show', ['token' => $token]);
        }
        return back()->withErrors(['pin' => 'Invalid PIN']);
    }

    public function publicPdf(string $token, Request $request)
    {
        $rx = Prescription::with(['items', 'patient', 'createdBy'])->where('share_token', $token)->firstOrFail();
        if ($rx->share_expires_at && $rx->share_expires_at->isPast()) {
            abort(404);
        }

        $config = AdditionalExportConfigs::getPrescriptionConfig();
        return $this->handlePrintSingle($request, $rx, $config);
    }
}
