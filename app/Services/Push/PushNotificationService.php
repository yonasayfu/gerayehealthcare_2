<?php

namespace App\Services\Push;

use App\Models\PushToken;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PushNotificationService
{
    protected const FCM_ENDPOINT = 'https://fcm.googleapis.com/fcm/send';
    protected const MAX_TOKENS_PER_REQUEST = 500;

    public function sendToUser(
        User $user,
        string $title,
        string $body,
        array $data = [],
        string $channel = 'general'
    ): void {
        $tokens = $user->pushTokens()->pluck('token')->filter()->unique()->values()->all();

        if (empty($tokens)) {
            return;
        }

        $this->sendToTokens($tokens, $title, $body, $data, $channel);
    }

    public function sendToTokens(
        array $tokens,
        string $title,
        string $body,
        array $data = [],
        string $channel = 'general'
    ): void {
        $tokens = array_values(array_filter(array_unique($tokens)));
        if (empty($tokens)) {
            return;
        }

        $serverKey = Config::get('services.fcm.server_key');
        if (empty($serverKey)) {
            Log::warning('FCM server key not configured; skipping push notification.');
            return;
        }

        $payload = [
            'notification' => [
                'title' => $title,
                'body' => $body,
                'sound' => 'default',
                'channel_id' => $channel,
              ],
            'data' => array_merge([
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                'channel' => $channel,
            ], $data),
        ];

        foreach (array_chunk($tokens, self::MAX_TOKENS_PER_REQUEST) as $batch) {
            $response = Http::withHeaders([
                'Authorization' => 'key=' . $serverKey,
                'Content-Type' => 'application/json',
            ])->post(self::FCM_ENDPOINT, array_merge($payload, [
                'registration_ids' => $batch,
            ]));

            if ($response->failed()) {
                Log::warning('Failed to send FCM push notification', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            }
        }
    }
}
