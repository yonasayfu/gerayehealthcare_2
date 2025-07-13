<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str; // Import Str facade for UUID generation

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name',
        'fayda_id',          // Make sure this is fillable if set via form
        'date_of_birth',
        'gender',
        'address',
        'phone_number',
        'email',             // Make sure this is fillable
        'emergency_contact',
        'source',
        'geolocation',
        'registered_by_staff_id', // Add if using staff to register patients
        'registered_by_caregiver_id', // Add if using caregivers to register patients
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        // Automatically generate a unique patient_code before creation
        static::creating(function ($patient) {
            if (empty($patient->patient_code)) {
                $patient->patient_code = (string) Str::uuid(); // Generates a UUID
                // Alternative for a simpler, less secure but unique code:
                // $patient->patient_code = 'PAT-' . uniqid();
            }
        });
    }

    /**
     * Get the staff member who registered the patient.
     * Assumes a 'staff' table and a 'registered_by_staff_id' column on 'patients'.
     */
    public function registeredByStaff()
    {
        // Adjust 'App\Models\Staff' if your Staff model is in a different namespace
        return $this->belongsTo(Staff::class, 'registered_by_staff_id');
    }

    /**
     * Get the caregiver who registered the patient.
     * Assumes a 'caregivers' table and a 'registered_by_caregiver_id' column on 'patients'.
     */
    public function registeredByCaregiver()
    {
        // Adjust 'App\Models\Caregiver' if your Caregiver model is in a different namespace
        return $this->belongsTo(Caregiver::class, 'registered_by_caregiver_id');
    }

    // You can also add a polymorphic relationship if a patient can be registered
    // by multiple types of entities (Staff, Caregiver, Admin, etc.)
    /*
    public function registeredBy()
    {
        return $this->morphTo();
    }
    */
}

  