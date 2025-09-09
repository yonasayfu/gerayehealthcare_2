<?php

namespace App\Notifications;

use App\Models\LeaveRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class LeaveRequestStatusUpdated extends Notification
{
    use Queueable;

    public function __construct(public LeaveRequest $leaveRequest) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'leave_request_status',
            'message_preview' => sprintf('Your leave request %s to %s is %s',
                $this->leaveRequest->start_date, $this->leaveRequest->end_date, $this->leaveRequest->status
            ),
            'leave_request_id' => $this->leaveRequest->id,
            'status' => $this->leaveRequest->status,
            'url' => route('staff.leave-requests.index'),
        ];
    }
}

