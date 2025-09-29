<?php

namespace App\Services;

use App\Models\NotificationSetting;
use App\Models\User;

class NotificationPreferenceService
{
    public function get(User $user): NotificationSetting
    {
        return $user->notificationSettings()->firstOrCreate([
            'user_id' => $user->id,
        ]);
    }

    public function update(User $user, array $payload): NotificationSetting
    {
        $settings = $this->get($user);
        $settings->fill($payload);
        $settings->save();

        return $settings;
    }
}
