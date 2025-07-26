<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InventoryAlert extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'alert_type',
        'threshold_value',
        'message',
        'is_active',
        'triggered_at',
    ];

    public function item()
    {
        return $this->belongsTo(InventoryItem::class);
    }
}