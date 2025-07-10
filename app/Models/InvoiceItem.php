<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'visit_service_id',
        'description',
        'cost',
    ];

    /**
     * The invoice that this item belongs to.
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * The original visit service this item represents.
     */
    public function visitService(): BelongsTo
    {
        return $this->belongsTo(VisitService::class);
    }
}