<?php

namespace App\Notifications;

use App\Models\LeaveRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class LeaveRequestSubmitted extends Notification
{
    use Queueable;

    public function __construct(public LeaveRequest $leaveRequest) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $staff = $this->leaveRequest->staff;

        return [
            'type' => 'leave_request_submitted',
            'message_preview' => sprintf('%s %s requested leave %s to %s',
                $staff->first_name ?? 'Staff', $staff->last_name ?? '',
                $this->leaveRequest->start_date, $this->leaveRequest->end_date
            ),
            'staff_id' => $this->leaveRequest->staff_id,
            'leave_request_id' => $this->leaveRequest->id,
            'url' => route('admin.leave-requests.index'),
        ];
    }
}

