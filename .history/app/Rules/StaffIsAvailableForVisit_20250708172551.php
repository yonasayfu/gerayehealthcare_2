<?php

namespace App\Rules;

use App\Models\StaffAvailability;
use App\Models\VisitService;
use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\Rule;

class StaffIsAvailableForVisit implements Rule, DataAwareRule
{
    /**
     * All of the data under validation.
     *
     * @var array<string, mixed>
     */
    protected $data = [];

    /**
     * Set the data under validation.
     */
    public function setData(array $data): static
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Get the necessary data from the form
        $staffId = $value; // This is the staff_id being validated
        $scheduledAt = Carbon::parse($this->data['scheduled_at'] ?? null);
        $visitIdToIgnore = $this->data['visit_id'] ?? null; // This will be set when editing a visit

        // If scheduled_at is not provided, we can't validate.
        if (!($this->data['scheduled_at'] ?? null)) {
            return; // Let the 'required' rule on the controller handle this.
        }

        // Assumption: We are defining a standard visit duration of 1 hour for conflict checking.
        $visitStartTime = $scheduledAt;
        $visitEndTime = $scheduledAt->copy()->addHour();

        // --- Check 1: Conflict with the staff's declared "Unavailable" slots ---
        $isUnavailable = StaffAvailability::where('staff_id', $staffId)
            ->where('status', 'Unavailable')
            ->where(function ($query) use ($visitStartTime, $visitEndTime) {
                // This logic finds any overlap between the new visit and an unavailable slot.
                $query->where('start_time', '<', $visitEndTime)
                      ->where('end_time', '>', $visitStartTime);
            })
            ->exists();

        if ($isUnavailable) {
            $fail('This staff member has marked themselves as unavailable during this time.');
            return;
        }

        // --- Check 2: Conflict with another Visit Service already scheduled ---
        $hasConflict = VisitService::where('staff_id', $staffId)
            ->when($visitIdToIgnore, function ($query) use ($visitIdToIgnore) {
                // If we are editing a visit, ignore it from the conflict check.
                return $query->where('id', '!=', $visitIdToIgnore);
            })
            ->where(function ($query) use ($visitStartTime, $visitEndTime) {
                // We also assume a 1-hour duration for existing visits for this check.
                $query->where('scheduled_at', '<', $visitEndTime)
                      ->where(
                          (new VisitService)->getTable().'.scheduled_at', // Explicitly state table to avoid ambiguity
                          '>',
                          $visitStartTime->copy()->subHour()
                      );
            })
            ->exists();
            
        if ($hasConflict) {
            $fail('This staff member is already scheduled for another visit at this time.');
        }
    }
}