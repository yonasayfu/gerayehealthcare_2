<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'request_id',
        'from_location',
        'to_location',
        'from_assigned_to_type',
        'from_assigned_to_id',
        'to_assigned_to_type',
        'to_assigned_to_id',
        'quantity',
        'transaction_type',
        'performed_by_id',
        'notes',
    ];

    public function item()
    {
        return $this->belongsTo(InventoryItem::class);
    }

    public function request()
    {
        return $this->belongsTo(InventoryRequest::class);
    }

    public function performedBy()
    {
        return $this->belongsTo(Staff::class, 'performed_by_id');
    }

    public function fromAssignedTo()
    {
        return $this->morphTo(__FUNCTION__, 'from_assigned_to_type', 'from_assigned_to_id');
    }

    public function toAssignedTo()
    {
        return $this->morphTo(__FUNCTION__, 'to_assigned_to_type', 'to_assigned_to_id');
    }
}
