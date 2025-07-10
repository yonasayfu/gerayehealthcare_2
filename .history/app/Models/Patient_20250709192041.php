<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name',
        'date_of_birth',
        'gender',
        'address',
        'phone_number',
        'email',
        'emergency_contact',
        'geolocation',
    ];
    
}
