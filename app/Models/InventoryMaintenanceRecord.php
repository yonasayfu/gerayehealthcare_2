<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryMaintenanceRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'scheduled_date',
        'actual_date',
        'performed_by_staff_id', // Changed to foreign key
        'cost',
        'description',
        'next_due_date',
        'status',
    ];

    public function item()
    {
        return $this->belongsTo(InventoryItem::class);
    }

    public function performedByStaff()
    {
        return $this->belongsTo(Staff::class, 'performed_by_staff_id');
    }
}
