<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReferralDocument;
use App\Services\ReferralDocumentService;
use App\DTOs\CreateReferralDocumentDTO;
use App\DTOs\UpdateReferralDocumentDTO;
use Illuminate\Http\Request;
use App\Models\Referral;
use App\Models\Staff;
use Inertia\Inertia;

class ReferralDocumentController extends Controller
{
    protected $referralDocumentService;

    public function __construct(ReferralDocumentService $referralDocumentService)
    {
        $this->referralDocumentService = $referralDocumentService;
    }

    public function index()
    {
        return Inertia::render('Admin/ReferralDocuments/Index', [
            'referralDocuments' => ReferralDocument::with(['referral', 'uploadedBy'])->latest()->paginate(10),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/ReferralDocuments/Create', [
            'referrals' => Referral::all(),
            'staff' => Staff::all(),
        ]);
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'referral_id' => 'required|exists:referrals,id',
                'document_name' => 'required|string|max:255',
                'document_file' => 'required|file|mimes:pdf,doc,docx,jpg,png',
                'document_type' => 'nullable|string|max:100',
                'status' => 'required|string|max:50',
            ]);

            $dto = new CreateReferralDocumentDTO(
                referral_id: $request->input('referral_id'),
                uploaded_by_staff_id: auth()->id(), // Assuming authenticated staff
                document_name: $request->input('document_name'),
                document_file: $request->file('document_file'),
                document_type: $request->input('document_type'),
                status: $request->input('status')
            );

            $this->referralDocumentService->createReferralDocument($dto);

            return redirect()->route('admin.referral-documents.index')->with('success', 'Referral document created successfully.');
        } catch (\Exception $e) {
            file_put_contents(storage_path('logs/referral_document_error.log'), $e->getMessage() . "\n" . $e->getTraceAsString(), FILE_APPEND);
            return back()->withErrors(['error' => 'An error occurred while creating the referral document.']);
        }
    }

    public function show(ReferralDocument $referralDocument)
    {
        return Inertia::render('Admin/ReferralDocuments/Show', [
            'referralDocument' => $referralDocument->load(['referral', 'uploadedBy'])
        ]);
    }

    public function edit(ReferralDocument $referralDocument)
    {
        return Inertia::render('Admin/ReferralDocuments/Edit', [
            'referralDocument' => $referralDocument,
            'referrals' => Referral::all(),
            'staff' => Staff::all(),
        ]);
    }

    public function update(Request $request, ReferralDocument $referralDocument)
    {
        $request->validate([
            'referral_id' => 'sometimes|required|exists:referrals,id',
            'document_name' => 'sometimes|required|string|max:255',
            'document_file' => 'nullable|file|mimes:pdf,doc,docx,jpg,png',
            'document_type' => 'nullable|string|max:100',
            'status' => 'sometimes|required|string|max:50',
        ]);

        $dto = new UpdateReferralDocumentDTO(
            referral_id: $request->input('referral_id'),
            document_name: $request->input('document_name'),
            document_file: $request->file('document_file'),
            document_type: $request->input('document_type'),
            status: $request->input('status')
        );

        $this->referralDocumentService->updateReferralDocument($referralDocument, $dto);

        return redirect()->route('admin.referral-documents.index')->with('success', 'Referral document updated successfully.');
    }

    public function destroy(ReferralDocument $referralDocument)
    {
        $this->referralDocumentService->deleteReferralDocument($referralDocument);

        return redirect()->route('admin.referral-documents.index')->with('success', 'Referral document deleted successfully.');
    }

    public function export(Request $request)
    {
        return $this->referralDocumentService->export($request);
    }

    public function printAll(Request $request)
    {
        return $this->referralDocumentService->printAll($request);
    }

    public function printCurrent(Request $request)
    {
        return $this->referralDocumentService->printCurrent($request);
    }

    public function printSingle(ReferralDocument $referralDocument, Request $request)
    {
        return $this->referralDocumentService->printSingle($referralDocument, $request);
    }
}
