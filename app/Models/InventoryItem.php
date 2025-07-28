<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InventoryItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'item_category',
        'item_type',
        'serial_number',
        'purchase_date',
        'warranty_expiry',
        'supplier_id',
        'assigned_to_type',
        'assigned_to_id',
        'last_maintenance_date',
        'next_maintenance_due',
        'maintenance_schedule',
        'notes',
        'status',
        'quantity_on_hand',
        'reorder_level',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function assignedTo()
    {
        return $this->morphTo();
    }

    public function requests()
    {
        return $this->hasMany(InventoryRequest::class, 'item_id');
    }

    public function transactions()
    {
        return $this->hasMany(InventoryTransaction::class, 'item_id');
    }

    public function maintenanceRecords()
    {
        return $this->hasMany(InventoryMaintenanceRecord::class, 'item_id');
    }

    public function alerts()
    {
        return $this->hasMany(InventoryAlert::class, 'item_id');
    }
}