<?php

namespace App\Notifications;

use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Notification;

class NewMessageReceived extends Notification implements ShouldQueue
{
    use Queueable;

    protected Message $message;

    /**
     * Create a new notification instance.
     */
    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $channels = ['database'];

        // Add mail channel if user has email notifications enabled
        if ($notifiable->email_notifications ?? true) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $senderName = $this->message->sender->name;
        $messagePreview = $this->getMessagePreview();

        return (new MailMessage)
            ->subject("New message from {$senderName}")
            ->greeting("Hello {$notifiable->name}!")
            ->line("You have received a new message from {$senderName}.")
            ->line("Message: {$messagePreview}")
            ->action('View Message', url('/messages?recipient=' . $this->message->sender_id))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'new_message',
            'message_id' => $this->message->id,
            'sender_id' => $this->message->sender_id,
            'sender_name' => $this->message->sender->name,
            'sender_avatar' => $this->message->sender->profile_photo_path,
            'message_preview' => $this->getMessagePreview(),
            'has_attachment' => $this->message->hasAttachment(),
            'priority' => $this->message->priority,
            'created_at' => $this->message->created_at,
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

    /**
     * Get message preview for notification
     */
    private function getMessagePreview(): string
    {
        if ($this->message->message) {
            return strlen($this->message->message) > 100 
                ? substr($this->message->message, 0, 100) . '...'
                : $this->message->message;
        }

        if ($this->message->hasAttachment()) {
            return '📎 Sent an attachment: ' . $this->message->attachment_filename;
        }

        return 'New message';
    }

    /**
     * Determine if the notification should be sent.
     */
    public function shouldSend(object $notifiable, string $channel): bool
    {
        // Don't send notification if the receiver is the same as sender
        if ($this->message->receiver_id === $this->message->sender_id) {
            return false;
        }

        // Don't send email if user has disabled email notifications
        if ($channel === 'mail' && !($notifiable->email_notifications ?? true)) {
            return false;
        }

        return true;
    }
}
