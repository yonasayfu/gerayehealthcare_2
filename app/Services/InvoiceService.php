<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Patient;
use App\Models\VisitService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceService extends BaseService
{
    public function __construct(Invoice $invoice)
    {
        parent::__construct($invoice);
    }

    protected function applySearch($query, $search)
    {
        $query->where('invoice_number', 'ilike', "%{$search}%")
              ->orWhereHas('patient', function ($q) use ($search) {
                  $q->where('full_name', 'ilike', "%{$search}%");
              });
    }

    public function getAll(Request $request)
    {
        $query = $this->model->with(['patient:id,full_name']);

        if ($request->has('search')) {
            $this->applySearch($query, $request->input('search'));
        }

        if ($request->has('sort')) {
            $direction = $request->input('direction', 'asc');
            $query->orderBy($request->input('sort'), $direction);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        return $query->paginate($request->input('per_page', 10));
    }

    public function create(array|object $data): Invoice
    {
        $data = is_object($data) ? (array) $data : $data;
        return parent::create($data);
    }

    public function getById(int $id): Invoice
    {
        return $this->model->with(['patient', 'items.visitService.staff'])->findOrFail($id);
    }
}
