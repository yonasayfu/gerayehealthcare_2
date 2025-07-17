<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str; // Import Str facade for UUID generation
use Carbon\Carbon; // Import Carbon for date handling in age calculation

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name',
        'fayda_id',
        'date_of_birth',
        'gender',
        'address',
        'phone_number',
        'email', // Ensure 'email' is fillable if you want to store it
        'emergency_contact',
        'source',
        'geolocation',
        'patient_code', // It's good to keep this fillable too, in case you manually set it sometimes
        'registered_by_staff_id', // Add if using staff to register patients
        'registered_by_caregiver_id', // Add if using caregivers to register patients
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        // Automatically generate a unique patient_code before creation if it's empty
        static::creating(function ($patient) {
            if (empty($patient->patient_code)) {
                $patient->patient_code = (string) Str::uuid(); // Generates a UUID
            }
        });
    }

    /**
     * Get the full name of the patient.
     *
     * @return string
     */
    public function getFullNameAttribute()
    {
        // Return existing full_name or a default 'Unknown Patient'
        return $this->attributes['full_name'] ?? 'Unknown Patient';
    }

    /**
     * Get the patient's age based on date_of_birth.
     *
     * @return int|null
     */
    public function getAgeAttribute()
    {
        if (!$this->date_of_birth) {
            return null;
        }

        // Use Carbon to calculate age from date_of_birth to now
        return Carbon::parse($this->date_of_birth)->age;
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
        // Reverting to original as per foreign key constraint in migration
        return $this->belongsTo(CaregiverAssignment::class, 'registered_by_caregiver_id');
    }
}
