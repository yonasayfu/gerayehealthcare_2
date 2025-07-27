<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Inertia\Inertia;
use League\Csv\Writer;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Validator;
use Exception;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Supplier::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('contact_person', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
        }

        if ($request->filled('sort')) {
            $sortField = $request->input('sort');
            $sortDirection = $request->input('direction', 'asc');
            $query->orderBy($sortField, $sortDirection);
        }

        $suppliers = $query->paginate($request->input('per_page', 10))->withQueryString();

        return Inertia::render('Admin/Suppliers/Index', [
            'suppliers' => $suppliers,
            'filters' => $request->only(['search', 'sort', 'direction', 'per_page']),
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        $query = Supplier::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('contact_person', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
        }
        
        $suppliers = $query->get();

        $csv = Writer::createFromString('');
        $csv->insertOne(['ID', 'Name', 'Contact Person', 'Email', 'Phone', 'Address']);

        foreach ($suppliers as $supplier) {
            $csv->insertOne($supplier->toArray());
        }

        return response()->streamDownload(function () use ($csv) {
            echo $csv->toString();
        }, 'suppliers.csv', [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="suppliers.csv"',
        ]);
    }

    public function createImport()
    {
        return Inertia::render('Admin/Suppliers/Import');
    }

    public function storeImport(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $file = $request->file('csv_file');
        $path = $file->getRealPath();
        $records = array_map('str_getcsv', file($path));
        $header = array_shift($records);

        $rules = [
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string',
        ];

        $errors = [];
        foreach ($records as $index => $row) {
            $data = array_combine($header, $row);
            $validator = Validator::make($data, $rules);

            if ($validator->fails()) {
                $errors["Row " . ($index + 2)] = $validator->errors()->all();
            } else {
                Supplier::create($data);
            }
        }

        if (count($errors) > 0) {
            return redirect()->back()->withErrors($errors);
        }

        return redirect()->route('admin.suppliers.index')->with('success', 'Suppliers imported successfully.');
    }

    public function printAll(Request $request)
    {
        $query = Supplier::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('contact_person', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
        }

        $suppliers = $query->get();

        $pdf = Pdf::loadView('pdf.suppliers', ['suppliers' => $suppliers])->setPaper('a4', 'landscape');
        return $pdf->stream('suppliers.pdf');
    }

    public function show(Supplier $supplier)
    {
        return Inertia::render('Admin/Suppliers/Show', [
            'supplier' => $supplier,
        ]);
    }

    public function printSingle(Supplier $supplier)
    {
        return Inertia::render('Admin/Suppliers/PrintSingle', [
            'supplier' => $supplier,
        ]);
    }

    public function generateSinglePdf(Supplier $supplier)
    {
        $pdf = Pdf::loadView('pdf.suppliers_single_pdf', ['supplier' => $supplier]);
        return $pdf->stream('supplier_' . $supplier->id . '.pdf');
    }

    public function generatePdf(Request $request)
    {
        $query = Supplier::query();

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%$search%")
                  ->orWhere('contact_person', 'like', "%$search%");
        }

        $suppliers = $query->get();

        $pdf = Pdf::loadView('pdf.suppliers', ['suppliers' => $suppliers])->setPaper('a4', 'landscape');
        return $pdf->stream('suppliers.pdf');
    }

    public function create()
    {
        return Inertia::render('Admin/Suppliers/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string',
        ]);

        Supplier::create($validated);

        return redirect()->route('admin.suppliers.index')->with('success', 'Supplier created successfully.');
    }

    public function edit(Supplier $supplier)
    {
        return Inertia::render('Admin/Suppliers/Edit', [
            'supplier' => $supplier,
        ]);
    }

    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string',
        ]);

        $supplier->update($validated);

        return redirect()->route('admin.suppliers.index')->with('success', 'Supplier updated successfully.');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();

        return redirect()->route('admin.suppliers.index')->with('success', 'Supplier deleted successfully.');
    }
}