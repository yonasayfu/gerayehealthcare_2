<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SharedInvoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'partner_id',
        'shared_by_staff_id',
        'share_date',
        'status',
        'notes',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    public function sharedBy()
    {
        return $this->belongsTo(Staff::class, 'shared_by_staff_id');
    }
}
