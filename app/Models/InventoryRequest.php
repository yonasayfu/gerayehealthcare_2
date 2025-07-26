<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InventoryRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'requester_id',
        'approver_id',
        'item_id',
        'quantity_requested',
        'quantity_approved',
        'reason',
        'status',
        'priority',
        'needed_by_date',
        'approved_at',
        'fulfilled_at',
    ];

    public function requester()
    {
        return $this->belongsTo(Staff::class, 'requester_id');
    }

    public function approver()
    {
        return $this->belongsTo(Staff::class, 'approver_id');
    }

    public function item()
    {
        return $this->belongsTo(InventoryItem::class);
    }
}