<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB; // Import DB facade for transactions

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
        static::creating(function ($patient) {
            if (empty($patient->patient_code)) {
                // Use a transaction to ensure atomic generation of sequential patient code
                DB::transaction(function () use ($patient) {
                    $latestPatient = Patient::lockForUpdate() // Lock the table row for update
                                            ->whereNotNull('patient_code')
                                            ->orderBy('id', 'desc') // Order by ID to get the latest
                                            ->first();

                    $nextNumber = 1;
                    if ($latestPatient && preg_match('/PAT-(\d+)/', $latestPatient->patient_code, $matches)) {
                        $nextNumber = (int)$matches[1] + 1;
                    }

                    // Format the number with leading zeros, e.g., PAT-00001
                    $patient->patient_code = 'PAT-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
                });
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
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['age'];

    /**
     * Get the staff member who registered the patient.
     * Assumes a 'staff' table and a 'registered_by_staff_id' column on 'patients'.
     */
    public function registeredByStaff()
    {
        // Adjust 'App\Models\Staff' if your Staff model is in a different namespace
        return $this->belongsTo(Staff::class, 'registered_by_staff_id');
    }

}
