<?php

namespace App\Notifications;

use App\Models\LeaveRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LeaveRequestStatusUpdated extends Notification
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
        return [
            'type' => 'leave_request_status',
            'message_preview' => sprintf('(%s) Your leave request from %s to %s is now %s',
                $this->leaveRequest->type,
                $this->leaveRequest->start_date, $this->leaveRequest->end_date, $this->leaveRequest->status
            ),
            'leave_request_id' => $this->leaveRequest->id,
            'status' => $this->leaveRequest->status,
            'url' => route('staff.leave-requests.index', ['highlight' => $this->leaveRequest->id]),
        ];
    }

    public function toPush(object $notifiable): array
    {
        $payload = $this->toArray($notifiable);

        return [
            'title' => 'Leave request ' . strtolower($this->leaveRequest->status),
            'body' => $payload['message_preview'] ?? 'Your leave request has been updated.',
            'data' => $payload,
            'channel' => 'general',
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Leave Request ' . $this->leaveRequest->status . ' (' . $this->leaveRequest->type . ')')
            ->greeting('Hello ' . ($notifiable->name ?? 'Staff') . '!')
            ->line(sprintf('Your leave request from %s to %s has been %s.',
                $this->leaveRequest->start_date, $this->leaveRequest->end_date, strtolower($this->leaveRequest->status)))
            ->line('Type: ' . $this->leaveRequest->type)
            ->line(sprintf('Admin Notes: %s', $this->leaveRequest->admin_notes ?? 'N/A'))
            ->action('View Your Leave Requests', route('staff.leave-requests.index', ['highlight' => $this->leaveRequest->id]))
            ->line('Thank you for your patience.');
    }
}
