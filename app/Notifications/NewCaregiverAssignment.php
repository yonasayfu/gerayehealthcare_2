<?php

namespace App\Notifications;

use App\Models\CaregiverAssignment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewCaregiverAssignment extends Notification
{
    use Queueable;

    public $assignment;

    /**
     * Create a new notification instance.
     */
    public function __construct(CaregiverAssignment $assignment)
    {
        $this->assignment = $assignment;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Caregiver Assignment')
            ->line('You have been assigned a new patient.')
            ->line('Patient: '.$this->assignment->patient->full_name)
            ->line('Shift: '.$this->assignment->shift_start.' to '.$this->assignment->shift_end)
            ->action('View Assignment', route('staff.my-visits.index'))
            ->line('Thank you for your hard work!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'assignment_id' => $this->assignment->id,
            'patient_name' => $this->assignment->patient->full_name,
            'message' => 'You have a new assignment with '.$this->assignment->patient->full_name.'.',
            'url' => route('staff.my-visits.index'),
        ];
    }

    public function toPush(object $notifiable): array
    {
        $payload = $this->toArray($notifiable);

        return [
            'title' => 'New caregiver assignment',
            'body' => $payload['message'] ?? 'You have a new caregiver assignment.',
            'data' => $payload,
            'channel' => 'general',
        ];
    }
}
