<?php

namespace App\Http\Controllers\Insurance;

use App\Http\Controllers\Controller;
use App\Models\ExchangeRate;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Inertia\Inertia;

class ExchangeRateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ExchangeRate::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('currency_code', 'ilike', "%{$search}%")
                  ->orWhere('source', 'ilike', "%{$search}%");
        }

        if ($request->filled('sort') && !empty($request->input('sort'))) {
            $query->orderBy($request->input('sort'), $request->input('direction', 'asc'));
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $exchangeRates = $query->paginate($request->input('per_page', 5))->withQueryString();

        return Inertia::render('Insurance/ExchangeRates/Index', [
            'exchangeRates' => $exchangeRates,
            'filters' => $request->only(['search', 'sort', 'direction', 'per_page']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Insurance/ExchangeRates/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'currency_code' => 'required|string|max:10',
            'rate_to_etb' => 'required|numeric',
            'source' => 'nullable|string|max:255',
            'date_effective' => 'required|date',
        ]);

        ExchangeRate::create($validated);

        return Redirect::route('admin.exchange-rates.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $exchangeRate = ExchangeRate::findOrFail($id);
        return Inertia::render('Insurance/ExchangeRates/Show', [
            'exchangeRate' => $exchangeRate,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $exchangeRate = ExchangeRate::findOrFail($id);
        return Inertia::render('Insurance/ExchangeRates/Edit', [
            'exchangeRate' => $exchangeRate,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $exchangeRate = ExchangeRate::findOrFail($id);

        $validated = $request->validate([
            'currency_code' => 'required|string|max:10',
            'rate_to_etb' => 'required|numeric',
            'source' => 'nullable|string|max:255',
            'date_effective' => 'required|date',
        ]);

        $exchangeRate->update($validated);

        return Redirect::route('admin.exchange-rates.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $exchangeRate = ExchangeRate::findOrFail($id);

        $exchangeRate->delete();

        return Redirect::route('admin.exchange-rates.index');
    }

    public function export(Request $request)
    {
        $type = $request->get('type');
        $exchangeRates = ExchangeRate::select('currency_code', 'rate_to_etb', 'source', 'date_effective')->get();

        if ($type === 'csv') {
            $csvData = "Currency Code,Rate to ETB,Source,Date Effective\n";
            foreach ($exchangeRates as $rate) {
                $csvData .= "\"{$rate->currency_code}\",\"{$rate->rate_to_etb}\",\"{$rate->source}\",\"{$rate->date_effective}\"\n";
            }

            return Response::make($csvData, 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename=\"exchange_rates.csv\"',
            ]);
        }

        if ($type === 'pdf') {
            $pdf = Pdf::loadView('pdf.exchange_rates', ['exchangeRates' => $exchangeRates])->setPaper('a4', 'landscape');
            return $pdf->stream('exchange_rates.pdf');
        }

        return abort(400, 'Invalid export type');
    }

    public function printSingle(ExchangeRate $exchangeRate)
    {
        $pdf = Pdf::loadView('pdf.exchange_rate_single', ['exchangeRate' => $exchangeRate])->setPaper('a4', 'portrait');
        return $pdf->stream("exchange_rate-{$exchangeRate->id}.pdf");
    }

    public function printCurrent(Request $request)
    {
        $query = ExchangeRate::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('currency_code', 'ilike', "%{$search}%")
                  ->orWhere('source', 'ilike', "%{$search}%");
        }

        if ($request->filled('sort') && !empty($request->input('sort'))) {
            $query->orderBy($request->input('sort'), $request->input('direction', 'asc'));
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $exchangeRates = $query->paginate($request->input('per_page', 5))->appends($request->except('page'));

        return Inertia::render('Insurance/ExchangeRates/PrintCurrent', ['exchangeRates' => $exchangeRates->items()]);
    }

    public function printAll(Request $request)
    {
        $exchangeRates = ExchangeRate::orderBy('currency_code')->get();

        $pdf = Pdf::loadView('pdf.exchange_rates', ['exchangeRates' => $exchangeRates])->setPaper('a4', 'landscape');
        return $pdf->stream('exchange_rates.pdf');
    }
}
