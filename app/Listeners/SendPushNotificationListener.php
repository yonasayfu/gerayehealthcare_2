<?php

namespace App\Listeners;

use App\Models\User;
use App\Services\Push\PushNotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Events\NotificationSent;
use Illuminate\Queue\InteractsWithQueue;

class SendPushNotificationListener implements ShouldQueue
{
    use InteractsWithQueue, Queueable;

    public function __construct(private PushNotificationService $pushService)
    {
    }

    public function handle(NotificationSent $event): void
    {
        if ($event->channel !== 'database') {
            return;
        }

        if (! $event->notifiable instanceof User) {
            return;
        }

        if (! method_exists($event->notification, 'toPush')) {
            return;
        }

        $payload = $event->notification->toPush($event->notifiable);
        if (! is_array($payload)) {
            return;
        }

        $title = (string) ($payload['title'] ?? 'Geraye Healthcare');
        $body = (string) ($payload['body'] ?? '');
        $data = (array) ($payload['data'] ?? []);
        $channel = (string) ($payload['channel'] ?? 'general');

        $this->pushService->sendToUser($event->notifiable, $title, $body, $data, $channel);
    }
}
