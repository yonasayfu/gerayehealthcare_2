<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany; // Import HasMany

class Staff extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'position',
        'department',
        'role',
        'status',
        'hire_date',
        'photo',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all of the availability slots for the staff member.
     * THIS IS THE NEW METHOD THAT FIXES THE ERROR.
     */
    public function availabilities(): HasMany
    {
        return $this->hasMany(StaffAvailability::class);
    }
}