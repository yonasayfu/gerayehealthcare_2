<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Traits\HasRoles;

class Staff extends Model
{
    use HasFactory, SoftDeletes, HasRoles;

    /**
     * The table associated with the model.
     */
    protected $table = 'staff';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'employee_id',
        'first_name',
        'last_name',
        'email',
        'phone_number',
        'position',
        'department',
        'hire_date',
        'salary',
        'profile_photo_path',
        'address',
        'emergency_contact_name',
        'emergency_contact_phone',
        'employment_type',
        'status',
        'is_active',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'hire_date' => 'date',
        'salary' => 'decimal:2',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * The attributes that should be appended to arrays.
     */
    protected $appends = [
        'full_name',
        'formatted_salary',
        'profile_photo_url',
        'years_of_service',
    ];

    /**
     * Employment types
     */
    const EMPLOYMENT_TYPES = [
        'full-time' => 'Full Time',
        'part-time' => 'Part Time',
        'contract' => 'Contract',
        'intern' => 'Intern',
    ];

    /**
     * Status types
     */
    const STATUS_TYPES = [
        'active' => 'Active',
        'inactive' => 'Inactive',
        'terminated' => 'Terminated',
        'on-leave' => 'On Leave',
    ];

    /**
     * Get the user that owns the staff profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include active staff.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->where('status', 'active');
    }

    /**
     * Scope a query to filter by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to filter by department.
     */
    public function scopeByDepartment($query, $department)
    {
        return $query->where('department', $department);
    }

    /**
     * Scope a query to filter by employment type.
     */
    public function scopeByEmploymentType($query, $type)
    {
        return $query->where('employment_type', $type);
    }

    /**
     * Scope a query to search staff by multiple fields.
     */
    public function scopeSearch($query, $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('first_name', 'LIKE', "%{$term}%")
                ->orWhere('last_name', 'LIKE', "%{$term}%")
                ->orWhere('email', 'LIKE', "%{$term}%")
                ->orWhere('employee_id', 'LIKE', "%{$term}%")
                ->orWhere('position', 'LIKE', "%{$term}%")
                ->orWhere('department', 'LIKE', "%{$term}%")
                ->orWhere('phone_number', 'LIKE', "%{$term}%");
        });
    }

    /**
     * Get the staff member's full name.
     */
    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    /**
     * Get the staff member's formatted salary.
     */
    public function getFormattedSalaryAttribute(): string
    {
        if (!$this->salary) {
            return 'Not specified';
        }
        return '$' . number_format($this->salary, 2);
    }

    /**
     * Get the profile photo URL.
     */
    public function getProfilePhotoUrlAttribute(): ?string
    {
        if (!$this->profile_photo_path) {
            return null;
        }
        return asset('storage/' . $this->profile_photo_path);
    }

    /**
     * Get years of service.
     */
    public function getYearsOfServiceAttribute(): string
    {
        if (!$this->hire_date) {
            return '0 years';
        }

        $years = $this->hire_date->diffInYears(now());
        $months = $this->hire_date->diffInMonths(now()) % 12;

        if ($years == 0) {
            return $months . ' month' . ($months != 1 ? 's' : '');
        }

        return $years . ' year' . ($years != 1 ? 's' : '') .
               ($months > 0 ? ', ' . $months . ' month' . ($months != 1 ? 's' : '') : '');
    }

    /**
     * Get employment type display name.
     */
    public function getEmploymentTypeDisplayAttribute(): string
    {
        return self::EMPLOYMENT_TYPES[$this->employment_type] ?? $this->employment_type;
    }

    /**
     * Get status display name.
     */
    public function getStatusDisplayAttribute(): string
    {
        return self::STATUS_TYPES[$this->status] ?? $this->status;
    }

    /**
     * Check if staff member is currently active.
     */
    public function isActive(): bool
    {
        return $this->is_active && $this->status === 'active';
    }

    /**
     * Generate unique employee ID.
     */
    public static function generateEmployeeId(): string
    {
        $prefix = 'EMP';
        $year = date('Y');

        // Get the last employee ID for this year
        $lastStaff = self::where('employee_id', 'LIKE', $prefix . $year . '%')
            ->orderBy('employee_id', 'desc')
            ->first();

        if ($lastStaff) {
            $lastNumber = (int) substr($lastStaff->employee_id, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . $year . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }
}
