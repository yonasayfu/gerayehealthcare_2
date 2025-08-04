<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Base\BaseController;
use App\Services\InvoiceService;
use App\Models\Invoice;
use App\Models\Patient;
use App\Models\VisitService;
use App\Services\Validation\Rules\InvoiceRules;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InvoiceController extends BaseController
{
    public function __construct(InvoiceService $invoiceService)
    {
        parent::__construct(
            $invoiceService,
            InvoiceRules::class,
            'Admin/Invoices',
            'invoices',
            Invoice::class,
            CreateInvoiceDTO::class
        );
    }

    
}
