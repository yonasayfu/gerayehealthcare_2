<?php

namespace App\Rules;

use App\Models\StaffAvailability;
use App\Models\VisitService;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\Rule;

class StaffIsAvailableForVisit implements Rule, DataAwareRule
{
    /**
     * All of the data under validation.
     * @var array<string, mixed>
     */
    protected $data = [];

    /**
     * The validation error message.
     * @var string
     */
    protected $message = '';

    /**
     * Set the data under validation.
     */
    public function setData(array $data): static
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        $staffId = $value;
        $scheduledAt = Carbon::parse($this->data['scheduled_at'] ?? null);
        $visitIdToIgnore = $this->data['visit_id'] ?? null;

        if (!($this->data['scheduled_at'] ?? null)) {
            return true; // Let the 'required' rule handle this.
        }

        // Assume a standard visit duration of 1 hour for conflict checking.
        $visitStartTime = $scheduledAt;
        $visitEndTime = $scheduledAt->copy()->addHour();

        // Check 1: Conflict with the staff's declared "Unavailable" slots
        $isUnavailable = StaffAvailability::where('staff_id', $staffId)
            ->where('status', 'Unavailable')
            ->where(function ($query) use ($visitStartTime, $visitEndTime) {
                $query->where('start_time', '<', $visitEndTime)
                      ->where('end_time', '>', $visitStartTime);
            })
            ->exists();

        if ($isUnavailable) {
            $this->message = 'This staff member has marked themselves as unavailable during this time.';
            return false;
        }

        // Check 2: Conflict with another Visit Service already scheduled
        $hasConflict = VisitService::where('staff_id', $staffId)
            ->when($visitIdToIgnore, function ($query) use ($visitIdToIgnore) {
                return $query->where('id', '!=', $visitIdToIgnore);
            })
            ->where(function ($query) use ($visitStartTime, $visitEndTime) {
                 // We also assume a 1-hour duration for existing visits for this check.
                 $query->where('scheduled_at', '<', $visitEndTime)
                      ->whereRaw('scheduled_at + INTERVAL \'1 hour\' > ?', [$visitStartTime]);
            })
            ->exists();
            
        if ($hasConflict) {
            $this->message = 'This staff member is already scheduled for another visit at this time.';
            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return $this->message;
    }
}