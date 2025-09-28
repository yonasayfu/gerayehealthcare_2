<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalVisit extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'visit_date',
        'visit_type',
        'doctor_id',
        'status',
        'notes',
    ];

    protected $casts = [
        'visit_date' => 'datetime',
    ];

    // Relationships
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Staff::class, 'doctor_id');
    }

    public function documents()
    {
        return $this->hasMany(MedicalDocument::class);
    }

    // Lightweight listing eager-load
    public function scopeWithList($query)
    {
        return $query->select(['id', 'patient_id', 'visit_date', 'visit_type', 'doctor_id', 'status'])
            ->with([
                'patient:id,full_name,patient_code',
                'doctor:id,first_name,last_name',
            ]);
    }
}
