<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SystemNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected array $data;

    /**
     * Create a new notification instance.
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $channels = ['database'];

        // Add mail channel if specified and user has email notifications enabled
        if (($this->data['send_email'] ?? false) && ($notifiable->email_notifications ?? true)) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $title = $this->data['title'] ?? 'System Notification';
        $message = $this->data['message'] ?? 'You have a new system notification.';
        $actionText = $this->data['action_text'] ?? 'View Details';
        $actionUrl = $this->data['action_url'] ?? url('/notifications');

        return (new MailMessage)
            ->subject($title)
            ->greeting("Hello {$notifiable->name}!")
            ->line($message)
            ->action($actionText, $actionUrl)
            ->line('Thank you for using our application!');
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => $this->data['type'] ?? 'system',
            'title' => $this->data['title'] ?? 'System Notification',
            'message' => $this->data['message'] ?? 'You have a new system notification.',
            'icon' => $this->data['icon'] ?? 'bell',
            'color' => $this->data['color'] ?? 'blue',
            'priority' => $this->data['priority'] ?? 'normal',
            'action_text' => $this->data['action_text'] ?? null,
            'action_url' => $this->data['action_url'] ?? null,
            'metadata' => $this->data['metadata'] ?? [],
            'created_at' => $this->data['created_at'] ?? now(),
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
