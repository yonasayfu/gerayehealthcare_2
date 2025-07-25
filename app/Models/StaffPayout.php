<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class StaffPayout extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_id',
        'total_amount',
        'payout_date',
        'status',
        'notes',
    ];

    protected $casts = [
        'payout_date' => 'date',
    ];

    /**
     * The staff member who received this payout.
     */
    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }

    /**
     * The visits included in this payout.
     */
    public function visitServices(): BelongsToMany
    {
        return $this->belongsToMany(VisitService::class, 'payout_visit_service');
    }
}