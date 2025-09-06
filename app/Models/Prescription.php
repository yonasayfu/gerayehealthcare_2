<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prescription extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'medical_document_id',
        'prescribed_date',
        'status',
        'instructions',
        'created_by_staff_id',
    ];

    protected $casts = [
        'prescribed_date' => 'date',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function document()
    {
        return $this->belongsTo(MedicalDocument::class, 'medical_document_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(Staff::class, 'created_by_staff_id');
    }

    public function items()
    {
        return $this->hasMany(PrescriptionItem::class);
    }

    public function scopeWithList($query)
    {
        return $query->select(['id', 'patient_id', 'prescribed_date', 'status'])
            ->with([
                'patient:id,full_name,patient_code',
            ]);
    }
}
