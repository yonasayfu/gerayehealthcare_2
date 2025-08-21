<?php

namespace App\Http\Controllers\Admin;

use App\DTOs\CreateReferralDocumentDTO;
use App\DTOs\UpdateReferralDocumentDTO;
use App\Http\Controllers\Controller;
use App\Models\Referral;
use App\Models\ReferralDocument;
use App\Models\Staff;
use App\Services\ReferralDocumentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ReferralDocumentController extends Controller
{
    protected $referralDocumentService;

    public function __construct(ReferralDocumentService $referralDocumentService)
    {
        $this->referralDocumentService = $referralDocumentService;
        // Policy-based authorization for resource routes
        $this->authorizeResource(ReferralDocument::class, 'referral_document');
        // Require staff linkage for write operations
        $this->middleware('ensure_staff_linked')->only(['store', 'update', 'destroy']);
    }

    public function index(Request $request)
    {
        $perPage = (int) $request->input('per_page', 5);
        $perPage = in_array($perPage, [5,10,25,50,100]) ? $perPage : 5;
        $search = (string) $request->input('search', '');
        $sort = (string) $request->input('sort', '');
        $direction = strtolower((string) $request->input('direction', 'desc')) === 'asc' ? 'asc' : 'desc';

        $sortable = [
            'id' => 'id',
            'document_name' => 'document_name',
            'document_type' => 'document_type',
            'status' => 'status',
            'created_at' => 'created_at',
        ];

        $query = ReferralDocument::with(['referral', 'uploadedBy']);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('document_name', 'ilike', "%{$search}%")
                    ->orWhere('document_type', 'ilike', "%{$search}%")
                    ->orWhere('status', 'ilike', "%{$search}%");
            });
        }

        if ($sort && isset($sortable[$sort])) {
            $query->orderBy($sortable[$sort], $direction);
        } else {
            $query->latest();
        }

        $referralDocuments = $query->paginate($perPage)->withQueryString();

        return Inertia::render('Admin/ReferralDocuments/Index', [
            'referralDocuments' => $referralDocuments,
            'filters' => [
                'search' => $search,
                'sort' => $sort,
                'direction' => $direction,
                'per_page' => $perPage,
            ],
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

            // Ensure we have a Staff id for the uploader. If missing, auto-provision a lightweight Staff linked to the user.
            $staffId = Staff::where('user_id', Auth::id())->value('id');
            if (!$staffId) {
                $user = Auth::user();
                $staff = Staff::create([
                    'first_name' => $user->name ?? 'System',
                    'last_name' => '',
                    'email' => $user->email,
                    'status' => 'Active',
                    'user_id' => $user->id,
                ]);
                $staffId = $staff->id;
            }

            $dto = new CreateReferralDocumentDTO(
                referral_id: $request->input('referral_id'),
                uploaded_by_staff_id: $staffId, // nullable if user isn't a staff member
                document_name: $request->input('document_name'),
                document_file: $request->file('document_file'),
                document_type: $request->input('document_type'),
                status: $request->input('status')
            );

            $this->referralDocumentService->createReferralDocument($dto);

            return redirect()->route('admin.referral-documents.index')->with('success', 'Referral document created successfully.');
        } catch (\Exception $e) {
            file_put_contents(storage_path('logs/referral_document_error.log'), $e->getMessage()."\n".$e->getTraceAsString(), FILE_APPEND);

            return back()->withErrors(['error' => 'An error occurred while creating the referral document.']);
        }
    }

    public function show(ReferralDocument $referralDocument)
    {
        return Inertia::render('Admin/ReferralDocuments/Show', [
            'referralDocument' => $referralDocument->load(['referral', 'uploadedBy']),
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
