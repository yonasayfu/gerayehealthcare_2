<?php

namespace App\Http\Controllers\Admin;

use App\DTOs\CreateSharedInvoiceDTO;
use App\DTOs\UpdateSharedInvoiceDTO;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Partner;
use App\Models\SharedInvoice;
use App\Models\Staff;
use App\Services\SharedInvoice\SharedInvoiceService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
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
        $status = $request->input('status');

        $sortable = [
            'id' => 'id',
            'share_date' => 'share_date',
            'status' => 'status',
            'created_at' => 'created_at',
            'share_views' => 'share_views',
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

        if ($status && $status !== 'All') {
            $query->where('status', $status);
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
                'status' => $status,
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
                // Use staff id if available; otherwise null
                shared_by_staff_id: optional(auth()->user()->staff)->id,
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

    /**
     * Generate or return a shareable public link for a shared invoice.
     */
    public function shareLink(SharedInvoice $sharedInvoice)
    {
        $now = Carbon::now();
        if (! $sharedInvoice->share_token || ($sharedInvoice->share_expires_at && $sharedInvoice->share_expires_at->isPast())) {
            $sharedInvoice->share_token = Str::random(48);
            // Default expiry in 30 days; adjust as needed
            $sharedInvoice->share_expires_at = $now->copy()->addDays(30);
            $sharedInvoice->save();
        }

        $url = route('public.shared-invoices.show', ['token' => $sharedInvoice->share_token]);

        return response()->json(['url' => $url, 'expires_at' => optional($sharedInvoice->share_expires_at)->toIso8601String()]);
    }

    /**
     * Public view by token (no auth). Validates expiry.
     */
    public function publicShow(string $token)
    {
        $sharedInvoice = SharedInvoice::with(['invoice', 'partner', 'sharedBy'])
            ->where('share_token', $token)
            ->first();

        if (! $sharedInvoice || ($sharedInvoice->share_expires_at && $sharedInvoice->share_expires_at->isPast())) {
            abort(404);
        }

        // Optional PIN gate
        if ($sharedInvoice->share_pin) {
            $sessionKey = 'shared_invoice_auth_'.$token;
            if (! session()->has($sessionKey)) {
                return view('public.shared-invoice-pin', ['token' => $token]);
            }
        }

        // Track view
        $sharedInvoice->increment('share_views');
        $sharedInvoice->last_viewed_at = now();
        $sharedInvoice->save();

        return view('public.shared-invoice', [
            'invoice' => $sharedInvoice,
        ]);
    }

    public function publicAuthenticate(Request $request, string $token)
    {
        $request->validate(['pin' => 'required|string|max:20']);
        $sharedInvoice = SharedInvoice::where('share_token', $token)->first();
        if (! $sharedInvoice || ($sharedInvoice->share_expires_at && $sharedInvoice->share_expires_at->isPast())) {
            abort(404);
        }

        if (hash_equals((string) $sharedInvoice->share_pin, (string) $request->input('pin'))) {
            session()->put('shared_invoice_auth_'.$token, true);
            return redirect()->route('public.shared-invoices.show', ['token' => $token]);
        }

        return back()->withErrors(['pin' => 'Invalid PIN']);
    }

    public function rotateShareLink(SharedInvoice $sharedInvoice, Request $request)
    {
        $sharedInvoice->share_token = Str::random(48);
        $sharedInvoice->share_expires_at = Carbon::now()->addDays(30);
        $sharedInvoice->save();
        return response()->json(['ok' => true]);
    }

    public function expireShareLink(SharedInvoice $sharedInvoice)
    {
        $sharedInvoice->share_expires_at = Carbon::now();
        $sharedInvoice->save();
        return response()->json(['ok' => true]);
    }

    public function setSharePin(SharedInvoice $sharedInvoice, Request $request)
    {
        $request->validate(['pin' => 'nullable|string|max:20']);
        $sharedInvoice->share_pin = $request->input('pin');
        $sharedInvoice->save();
        return response()->json(['ok' => true]);
    }
}
