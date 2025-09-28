<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'medical_visit_id',
        'document_type',
        'title',
        'document_date',
        'file_path',
        'summary',
        'is_printed',
        'created_by_staff_id',
    ];

    protected $casts = [
        'document_date' => 'date',
        'is_printed' => 'boolean',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function visit()
    {
        return $this->belongsTo(MedicalVisit::class, 'medical_visit_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(Staff::class, 'created_by_staff_id');
    }

    public function prescription()
    {
        return $this->hasOne(Prescription::class);
    }

    public function scopeWithList($query)
    {
        return $query->select(['id', 'patient_id', 'document_type', 'title', 'document_date', 'created_by_staff_id'])
            ->with([
                'patient:id,full_name,patient_code',
                'createdBy:id,first_name,last_name',
            ]);
    }
}
