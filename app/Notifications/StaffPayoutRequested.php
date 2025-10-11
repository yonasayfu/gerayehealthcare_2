<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class StaffPayoutRequested extends Notification
{
    use Queueable;

    public function __construct(
        public int $payoutId,
        public string $staffName,
        public float $amount,
        public ?string $notes = null,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'staff_payout_requested',
            'payout_id' => $this->payoutId,
            'staff_name' => $this->staffName,
            'amount' => $this->amount,
            'notes' => $this->notes,
            'message' => sprintf('Payout request from %s for %0.2f', $this->staffName, $this->amount),
        ];
    }

    public function toPush(object $notifiable): array
    {
        $payload = $this->toDatabase($notifiable);

        return [
            'title' => 'Payout request submitted',
            'body' => $payload['message'] ?? 'A staff payout has been requested.',
            'data' => $payload,
            'channel' => 'general',
        ];
    }
}
