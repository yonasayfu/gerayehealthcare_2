<?php

namespace App\Services;

use App\DTOs\CreateSharedInvoiceDTO;
use App\DTOs\UpdateSharedInvoiceDTO;
use App\Http\Config\ExportConfig;
use App\Http\Traits\ExportableTrait;
use App\Models\SharedInvoice;
use Illuminate\Http\Request;

class SharedInvoiceService extends BaseService
{
    use ExportableTrait;

    public function __construct(SharedInvoice $sharedInvoice)
    {
        parent::__construct($sharedInvoice);
    }

    public function createSharedInvoice(CreateSharedInvoiceDTO $dto): SharedInvoice
    {
        return SharedInvoice::create([
            'invoice_id' => $dto->invoice_id,
            'partner_id' => $dto->partner_id,
            'shared_by_staff_id' => $dto->shared_by_staff_id,
            'share_date' => $dto->share_date,
            'status' => $dto->status,
            'notes' => $dto->notes,
        ]);
    }

    public function updateSharedInvoice(SharedInvoice $sharedInvoice, UpdateSharedInvoiceDTO $dto): SharedInvoice
    {
        $data = [];
        if ($dto->invoice_id) {
            $data['invoice_id'] = $dto->invoice_id;
        }
        if ($dto->partner_id) {
            $data['partner_id'] = $dto->partner_id;
        }
        if ($dto->share_date) {
            $data['share_date'] = $dto->share_date;
        }
        if ($dto->status) {
            $data['status'] = $dto->status;
        }
        if ($dto->notes) {
            $data['notes'] = $dto->notes;
        }

        $sharedInvoice->update($data);

        return $sharedInvoice;
    }

    public function deleteSharedInvoice(SharedInvoice $sharedInvoice): void
    {
        $sharedInvoice->delete();
    }

    public function export(Request $request)
    {
        return $this->handleExport($request, SharedInvoice::class, ExportConfig::getSharedInvoiceConfig());
    }

    public function printAll(Request $request)
    {
        return $this->handlePrintAll($request, SharedInvoice::class, ExportConfig::getSharedInvoiceConfig());
    }

    public function printCurrent(Request $request)
    {
        return $this->handlePrintCurrent($request, SharedInvoice::class, ExportConfig::getSharedInvoiceConfig());
    }

    public function printSingle(SharedInvoice $sharedInvoice, Request $request)
    {
        return $this->handlePrintSingle($request, $sharedInvoice, ExportConfig::getSharedInvoiceConfig());
    }

    protected function applySearch($query, $search)
    {
        $query->where('notes', 'ilike', "%{$search}%");
    }
}
