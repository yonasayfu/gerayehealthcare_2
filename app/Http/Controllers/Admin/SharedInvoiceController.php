<?php

namespace App\Http\Controllers\Admin;

use App\DTOs\CreateSharedInvoiceDTO;
use App\DTOs\UpdateSharedInvoiceDTO;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Partner;
use App\Models\SharedInvoice;
use App\Models\Staff;
use App\Services\SharedInvoiceService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SharedInvoiceController extends Controller
{
    protected $sharedInvoiceService;

    public function __construct(SharedInvoiceService $sharedInvoiceService)
    {
        $this->sharedInvoiceService = $sharedInvoiceService;
    }

    public function index(Request $request)
    {
        $perPage = (int) $request->input('per_page', 5);
        $perPage = in_array($perPage, [5, 10, 25, 50, 100]) ? $perPage : 5;
        $search = (string) $request->input('search', '');
        $sort = (string) $request->input('sort', '');
        $direction = strtolower((string) $request->input('direction', 'desc')) === 'asc' ? 'asc' : 'desc';

        $sortable = [
            'id' => 'id',
            'share_date' => 'share_date',
            'status' => 'status',
            'created_at' => 'created_at',
        ];

        $query = SharedInvoice::with(['invoice', 'partner', 'sharedBy']);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('status', 'ilike', "%{$search}%")
                    ->orWhere('notes', 'ilike', "%{$search}%")
                    ->orWhere('share_date', 'ilike', "%{$search}%")
                    ->orWhereHas('invoice', function ($qi) use ($search) {
                        $qi->where('invoice_number', 'ilike', "%{$search}%");
                    })
                    ->orWhereHas('partner', function ($qp) use ($search) {
                        $qp->where('name', 'ilike', "%{$search}%");
                    })
                    ->orWhereHas('sharedBy', function ($qs) use ($search) {
                        $qs->whereRaw("concat(first_name, ' ', last_name) ilike ?", ["%{$search}%"]);
                    });
            });
        }

        if ($sort && isset($sortable[$sort])) {
            $query->orderBy($sortable[$sort], $direction);
        } else {
            $query->latest();
        }

        $sharedInvoices = $query->paginate($perPage)->withQueryString();

        return Inertia::render('Admin/SharedInvoices/Index', [
            'sharedInvoices' => $sharedInvoices,
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
        return Inertia::render('Admin/SharedInvoices/Create', [
            'invoices' => Invoice::all(),
            'partners' => Partner::all(),
            'staff' => Staff::all(),
        ]);
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'invoice_id' => 'required|exists:invoices,id',
                'partner_id' => 'required|exists:partners,id',
                'share_date' => 'required|date',
                'status' => 'required|string|max:50',
                'notes' => 'nullable|string',
            ]);

            $dto = new CreateSharedInvoiceDTO(
                invoice_id: $request->input('invoice_id'),
                partner_id: $request->input('partner_id'),
                shared_by_staff_id: auth()->id(), // Assuming authenticated staff
                share_date: $request->input('share_date'),
                status: $request->input('status'),
                notes: $request->input('notes')
            );

            $this->sharedInvoiceService->createSharedInvoice($dto);

            return redirect()->route('admin.shared-invoices.index')->with('banner', 'Invoice shared successfully.')->with('bannerStyle', 'success');
        } catch (\Exception $e) {
            file_put_contents(storage_path('logs/shared_invoice_error.log'), $e->getMessage()."\n".$e->getTraceAsString(), FILE_APPEND);

            return back()->withErrors(['error' => 'An error occurred while sharing the invoice.']);
        }
    }

    public function show(SharedInvoice $sharedInvoice)
    {
        return Inertia::render('Admin/SharedInvoices/Show', [
            'sharedInvoice' => $sharedInvoice->load(['invoice', 'partner', 'sharedBy']),
        ]);
    }

    public function edit(SharedInvoice $sharedInvoice)
    {
        return Inertia::render('Admin/SharedInvoices/Edit', [
            'sharedInvoice' => $sharedInvoice,
            'invoices' => Invoice::all(),
            'partners' => Partner::all(),
            'staff' => Staff::all(),
        ]);
    }

    public function update(Request $request, SharedInvoice $sharedInvoice)
    {
        $request->validate([
            'invoice_id' => 'sometimes|required|exists:invoices,id',
            'partner_id' => 'sometimes|required|exists:partners,id',
            'share_date' => 'sometimes|required|date',
            'status' => 'sometimes|required|string|max:50',
            'notes' => 'nullable|string',
        ]);

        $dto = new UpdateSharedInvoiceDTO(
            invoice_id: $request->input('invoice_id'),
            partner_id: $request->input('partner_id'),
            share_date: $request->input('share_date'),
            status: $request->input('status'),
            notes: $request->input('notes')
        );

        $this->sharedInvoiceService->updateSharedInvoice($sharedInvoice, $dto);

        return redirect()->route('admin.shared-invoices.show', $sharedInvoice)->with('banner', 'Shared invoice updated successfully.')->with('bannerStyle', 'success');
    }

    public function destroy(SharedInvoice $sharedInvoice)
    {
        $this->sharedInvoiceService->deleteSharedInvoice($sharedInvoice);

        return redirect()->route('admin.shared-invoices.index')->with('success', 'Shared invoice deleted successfully.');
    }

    public function export(Request $request)
    {
        return $this->sharedInvoiceService->export($request);
    }

    public function printAll(Request $request)
    {
        return $this->sharedInvoiceService->printAll($request);
    }

    public function printCurrent(Request $request)
    {
        return $this->sharedInvoiceService->printCurrent($request);
    }

    public function printSingle(SharedInvoice $sharedInvoice, Request $request)
    {
        return $this->sharedInvoiceService->printSingle($sharedInvoice, $request);
    }
}
