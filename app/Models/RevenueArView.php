<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RevenueArView extends Model
{
    protected $table = 'revenue_ar_view';

    public $timestamps = false;

    protected $primaryKey = null;

    public $incrementing = false;

    protected $guarded = [];

    protected $casts = [
        'bucket_date' => 'date',
        'bucket_label' => 'string',
        'granularity' => 'string',
        'invoices_count' => 'integer',
        'total_billed' => 'decimal:2',
        'total_received' => 'decimal:2',
        'ar_outstanding' => 'decimal:2',
        'paid_invoices' => 'integer',
        'unpaid_invoices' => 'integer',
    ];
}
