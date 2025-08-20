<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReferralDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'referral_id',
        'uploaded_by_staff_id',
        'document_name',
        'document_path',
        'original_name',
        'mime_type',
        'file_size',
        'checksum',
        'document_type',
        'status',
    ];

    public function referral()
    {
        return $this->belongsTo(Referral::class);
    }

    public function uploadedBy()
    {
        return $this->belongsTo(Staff::class, 'uploaded_by_staff_id');
    }
}
