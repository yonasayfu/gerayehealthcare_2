<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'invoice_number',
        'invoice_date',
        'due_date',
        'subtotal',
        'tax_amount',
        'grand_total',
        'status',
        'paid_at',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'paid_at' => 'datetime',
    ];

    /**
     * The "booted" method of the model.
     * Automatically generates a unique invoice number when creating a new invoice.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($invoice) {
            $latestInvoice = self::latest('id')->first();
            $nextId = $latestInvoice ? $latestInvoice->id + 1 : 1;
            $invoice->invoice_number = 'INV-' . str_pad($nextId, 5, '0', STR_PAD_LEFT);
        });
    }

    /**
     * The patient who this invoice belongs to.
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * The line items included in this invoice.
     */
    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }
}