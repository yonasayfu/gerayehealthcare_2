<?php

namespace App\Http\Controllers\Insurance;

use App\Http\Controllers\Controller;
use App\Http\Traits\ExportableTrait;
use App\Http\Config\AdditionalExportConfigs;
use App\Models\ExchangeRate;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Inertia\Inertia;
use App\Http\Requests\StoreExchangeRateRequest;
use App\Http\Requests\UpdateExchangeRateRequest;

class ExchangeRateController extends Controller
{
    use ExportableTrait;
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
    public function store(StoreExchangeRateRequest $request)
    {
        $validated = $request->validated();

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
    public function update(UpdateExchangeRateRequest $request, string $id)
    {
        $exchangeRate = ExchangeRate::findOrFail($id);

        $validated = $request->validated();

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
        return $this->handleExport($request, ExchangeRate::class, AdditionalExportConfigs::getExchangeRateConfig());
    }

    public function printSingle(ExchangeRate $exchangeRate)
    {
        return $this->handlePrintSingle($exchangeRate, AdditionalExportConfigs::getExchangeRateConfig());
    }

    public function printCurrent(Request $request)
    {
        return $this->handlePrintCurrent($request, ExchangeRate::class, AdditionalExportConfigs::getExchangeRateConfig());
    }

    public function printAll(Request $request)
    {
        return $this->handlePrintAll($request, ExchangeRate::class, AdditionalExportConfigs::getExchangeRateConfig());
    }
}
