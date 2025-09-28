<?php

namespace App\Notifications;

use App\Models\LeaveRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LeaveRequestSubmitted extends Notification
{
    use Queueable;

    public function __construct(public LeaveRequest $leaveRequest)
    {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toArray(object $notifiable): array
    {
        $staff = $this->leaveRequest->staff;

        return [
            'type' => 'leave_request_submitted',
            'message_preview' => sprintf('(%s) %s %s requested leave from %s to %s',
                $this->leaveRequest->type,
                $staff->first_name ?? 'Staff', $staff->last_name ?? '',
                $this->leaveRequest->start_date, $this->leaveRequest->end_date
            ),
            'staff_id' => $this->leaveRequest->staff_id,
            'leave_request_id' => $this->leaveRequest->id,
            'url' => route('admin.leave-requests.index', ['highlight' => $this->leaveRequest->id]),
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $staff = $this->leaveRequest->staff;

        return (new MailMessage)
            ->subject('New Leave Request Submitted (' . $this->leaveRequest->type . ')')
            ->greeting('Hello ' . ($notifiable->name ?? 'Admin') . '!')
            ->line(sprintf('%s %s has submitted a leave request.',
                $staff->first_name ?? 'Staff', $staff->last_name ?? ''))
            ->line(sprintf('Leave Period: %s to %s',
                $this->leaveRequest->start_date, $this->leaveRequest->end_date))
            ->line('Type: ' . $this->leaveRequest->type)
            ->line(sprintf('Reason: %s', $this->leaveRequest->reason ?? 'N/A'))
            ->action('Review Leave Request', route('admin.leave-requests.index', ['highlight' => $this->leaveRequest->id]))
            ->line('Please review and take appropriate action.');
    }
}
