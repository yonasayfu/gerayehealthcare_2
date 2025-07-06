<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\CaregiverAssignment;
use App\Models\StaffAvailability;

class StaffIsAvailableForShift implements ValidationRule
{
    protected $staffId;
    protected $shiftEnd;
    protected $ignoreAssignmentId;

    public function __construct($staffId, $shiftEnd, $ignoreAssignmentId = null)
    {
        $this->staffId = $staffId;
        $this->shiftEnd = $shiftEnd;
        $this->ignoreAssignmentId = $ignoreAssignmentId;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$this->staffId || !$this->shiftEnd) {
            return; // Cannot validate without staff_id and shift_end
        }

        $shiftStart = $value;

        // 1. Check for double booking (conflicting assignments)
        $conflictingAssignment = CaregiverAssignment::where('staff_id', $this->staffId)
            ->where(function ($query) use ($shiftStart) {
                $query->where('shift_start', '<', $this->shiftEnd)
                      ->where('shift_end', '>', $shiftStart);
            })
            // THIS IS THE FIX: If an ignore ID is provided, exclude it from the check.
            ->when($this->ignoreAssignmentId, function ($query) {
                $query->where('id', '!=', $this->ignoreAssignmentId);
            })
            ->exists();

        if ($conflictingAssignment) {
            $fail('This staff member is already assigned to another shift during this time.');
            return;
        }

        // 2. Check for unavailability records
        $isUnavailable = StaffAvailability::where('staff_id', $this->staffId)
            ->where('status', 'Unavailable')
            ->where(function ($query) use ($shiftStart) {
                $query->where('start_time', '<', $this->shiftEnd)
                      ->where('end_time', '>', $shiftStart);
            })
            ->exists();

        if ($isUnavailable) {
            $fail('This staff member has marked themselves as unavailable for this time period.');
            return;
        }
    }
}
