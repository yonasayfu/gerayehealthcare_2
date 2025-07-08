<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\Staff;
use App\Models\CaregiverAssignment;
use App\Models\StaffAvailability;

class StaffIsAvailableForShift implements ValidationRule
{
    protected $staffId;
    protected $shiftEnd;
    protected $ignoreAssignmentId;
    protected $conflictMessage = 'The staff member is not available for the selected time slot.';

    /**
     * Create a new rule instance.
     *
     * @param int $staffId
     * @param string $shiftEnd
     * @param int|null $ignoreAssignmentId
     */
    public function __construct($staffId, $shiftEnd, $ignoreAssignmentId = null)
    {
        $this->staffId = $staffId;
        $this->shiftEnd = $shiftEnd;
        $this->ignoreAssignmentId = $ignoreAssignmentId;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $shiftStart = $value; // $value is the shift_start time

        // 1. Check for double booking (conflicting assignments)
        $conflictingAssignment = CaregiverAssignment::where('staff_id', $this->staffId)
            ->where(function ($query) use ($shiftStart) {
                $query->where('shift_start', '<', $this->shiftEnd)
                      ->where('shift_end', '>', $shiftStart);
            })
            ->when($this->ignoreAssignmentId, function ($query) {
                $query->where('id', '!=', $this->ignoreAssignmentId);
            })
            ->exists();

        if ($conflictingAssignment) {
            $this->conflictMessage = 'This staff member is already assigned to another shift during this time.';
            $fail($this->conflictMessage);
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
            $this->conflictMessage = 'This staff member has marked themselves as unavailable for this time period.';
            $fail($this->conflictMessage);
            return;
        }

        // 3. Check for positive availability (the staff member MUST have an 'Available' slot covering the shift)
        $isAvailable = StaffAvailability::where('staff_id', $this->staffId)
            ->where('status', 'Available')
            ->where('start_time', '<=', $shiftStart)
            ->where('end_time', '>=', $this->shiftEnd)
            ->exists();

        if (!$isAvailable) {
