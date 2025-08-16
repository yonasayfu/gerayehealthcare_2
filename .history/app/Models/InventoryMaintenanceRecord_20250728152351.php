<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InventoryMaintenanceRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'scheduled_date',
        'actual_date',
        'performed_by',
        'cost',
        'description',
        'next_due_date',
        'status',
    ];

    public function item()
    {
        return $this->belongsTo(InventoryItem::class);
    }
}