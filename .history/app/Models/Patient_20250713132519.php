<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'email',
        'emergency_contact',
        
        'geolocation',
    ];
    /**
     * Get all of the invoices for the patient.
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }
}
